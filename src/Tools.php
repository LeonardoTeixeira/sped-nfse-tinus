<?php

namespace NFePHP\NFSeTinus;

/**
 * Class for comunications with NFSe Tinus Provider
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeTinus
 * @copyright NFePHP Copyright (c) 2020
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Cleiton Perin <cperin20 at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-tinus for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\NFSeTinus\Common\Tools as BaseTools;

class Tools extends BaseTools
{
    const ERRO_EMISSAO = 1;
    const SERVICO_NAO_CONCLUIDO = 2;

    protected $xsdpath;

    public function __construct($config, Certificate $cert)
    {
        parent::__construct($config, $cert);
        //tinus não usa XSD
        /**
        $path = realpath(
            __DIR__ . '/../storage/schemes'
        );
        $this->xsdpath = $path . '/nfsetempuri.xsd';
         */
    }

    /**
     * Solicita o cancelamento de NFSe (SINCRONO)
     * @param integer $numero
     * @param integer $codigo
     * @return string
     */
    public function cancelarNfse($numero, $codigo = self::ERRO_EMISSAO, $id = null)
    {
        $operation = 'CancelarNfse';
        $pedido = "<Pedido>"
            . "<InfPedidoCancelamento>"
            . "<IdentificacaoNfse>"
            . "<Numero>$numero</Numero>"
            . "<Cnpj>" . $this->config->cnpj . "</Cnpj>"
            . "<InscricaoMunicipal>" . $this->config->im . "</InscricaoMunicipal>"
            . "<CodigoMunicipio>" . $this->config->cmun . "</CodigoMunicipio>"
            . "</IdentificacaoNfse>"
            . "<CodigoCancelamento>$codigo</CodigoCancelamento>"
            . "</InfPedidoCancelamento>"
            . "</Pedido>";

        $signed = $this->sign($pedido, 'InfPedidoCancelamento', '');
        $content = "<CancelarNfseEnvio>"
            . $signed
            . "</CancelarNfseEnvio>";
        //Tinus não usa XSD Validator::isValid($content, $this->xsdpath);
        return $this->send($content, $operation);
    }

    /**
     * Consulta Lote RPS (SINCRONO) após envio com recepcionarLoteRps() (ASSINCRONO)
     * complemento do processo de envio assincono.
     * Que deve ser usado quando temos mais de um RPS sendo enviado
     * por vez.
     * @param string $protocolo
     * @return string
     */
    public function consultarLoteRps($protocolo)
    {
        $operation = 'ConsultarLoteRps';
        $content = "<ConsultarLoteRpsEnvio>"
            . $this->prestador
            . "<Protocolo>$protocolo</Protocolo>"
            . "</ConsultarLoteRpsEnvio>";
        //Tinus não usa XSD Validator::isValid($content, $this->xsdpath);
        return $this->send($content, $operation);
    }

    /**
     * Consulta NFSe emitidas em um periodo e por tomador (SINCRONO)
     * @param string $dini
     * @param string $dfim
     * @param string $tomadorCnpj
     * @param string $tomadorCpf
     * @param string $tomadorIM
     * @return string
     */
    public function consultarNfse($dini, $dfim, $tomadorCnpj = null, $tomadorCpf = null, $tomadorIM = null)
    {
        $operation = 'ConsultarNfse';
        $content = "<ConsultarNfseEnvio>"
            . $this->prestador
            . "<PeriodoEmissao>"
            . "<DataInicial>$dini</DataInicial>"
            . "<DataFinal>$dfim</DataFinal>"
            . "</PeriodoEmissao>";

        if ($tomadorCnpj || $tomadorCpf) {
            $content .= "<Tomador>"
                . "<CpfCnpj>";
            if (isset($tomadorCnpj)) {
                $content .= "<Cnpj>$tomadorCnpj</Cnpj>";
            } else {
                $content .= "<Cpf>$tomadorCpf</Cpf>";
            }
            $content .= "</CpfCnpj>";
            if (isset($tomadorIM)) {
                $content .= "<InscricaoMunicipal>$tomadorIM</InscricaoMunicipal>";
            }
            $content .= "</Tomador>";
        }
        $content .= "</ConsultarNfseEnvio>";
        //Tinus não usa XSD Validator::isValid($content, $this->xsdpath);
        return $this->send($content, $operation);
    }

    /**
     * Consulta NFSe por RPS (SINCRONO)
     * @param integer $numero
     * @param string $serie
     * @param integer $tipo
     * @return string
     */
    public function consultarNfsePorRps($numero, $serie, $tipo)
    {
        $operation = "ConsultarNfsePorRps";
        $content = "<ConsultarNfseRpsEnvio>"
            . "<IdentificacaoRps>"
            . "<Numero>$numero</Numero>"
            . "<Serie>$serie</Serie>"
            . "<Tipo>$tipo</Tipo>"
            . "</IdentificacaoRps>"
            . $this->prestador
            . "</ConsultarNfseRpsEnvio>";
        //Tinus não usa XSD Validator::isValid($content, $this->xsdpath);
        return $this->send($content, $operation);
    }

    /**
     * Envia LOTE de RPS para emissão de NFSe (ASSINCRONO)
     * @param array $arps Array contendo de 1 a 50 RPS::class
     * @param string $lote Número do lote de envio
     * @return string
     * @throws \Exception
     */
    public function recepcionarLoteRps($arps, $lote)
    {
        $operation = 'RecepcionarLoteRps';
        $no_of_rps_in_lot = count($arps);
        if ($no_of_rps_in_lot > 50) {
            throw new \Exception('O limite é de 50 RPS por lote enviado.');
        }
        $content = '';
        foreach ($arps as $rps) {
            $rps->config($this->config);
            $content .= $rps->render();
        }
        $contentmsg = "<EnviarLoteRpsEnvio>"
            . "<LoteRps Id=\"$lote\">"
            . "<NumeroLote>$lote</NumeroLote>"
            . "<Cnpj>" . $this->config->cnpj . "</Cnpj>"
            . "<InscricaoMunicipal>" . $this->config->im . "</InscricaoMunicipal>"
            . "<QuantidadeRps>$no_of_rps_in_lot</QuantidadeRps>"
            . "<ListaRps>"
            . $content
            . "</ListaRps>"
            . "</LoteRps>"
            . "</EnviarLoteRpsEnvio>";
        $content = $this->sign($contentmsg, 'LoteRps', 'Id');
        //Tinus não usa XSD Validator::isValid($content, $this->xsdpath);
        return $this->send($content, $operation);
    }
}
