<?php

namespace NFePHP\NFSeTinus\Common;

/**
 * Auxiar Tools Class for comunications with NFSe webserver in Tinus Standard
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeTinus
 * @copyright NFePHP Copyright (c) 2008-2020
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Cleiton Perin <cperin20 at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-tinus for the canonical source repository
 */

use NFePHP\Common\Certificate;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSeTinus\Common\Soap\SoapCurl;
use NFePHP\NFSeTinus\Common\Soap\SoapInterface;
use NFePHP\NFSeTinus\RpsInterface;

class Tools
{
    public $lastRequest;

    protected $config;
    protected $prestador;
    protected $certificate;
    protected $wsobj;
    protected $soap;
    protected $environment;

    /**
     * Constructor
     * @param string $config
     * @param Certificate $cert
     */
    public function __construct($config, Certificate $cert)
    {
        $this->config = json_decode($config);
        $this->certificate = $cert;
        $this->buildPrestadorTag();
        $this->wsobj = $this->loadWsobj($this->config->cmun);
        $this->environment = 'homologacao';
        if ($this->config->tpamb === 1) {
            $this->environment = 'producao';
        }
    }

    /**
     * load webservice parameters
     * @param string $cmun
     * @return object
     * @throws \Exception
     */
    protected function loadWsobj($cmun)
    {
        $path = realpath(__DIR__ . "/../../storage/urls_webservices.json");
        $urls = json_decode(file_get_contents($path), true);
        if (empty($urls[$cmun])) {
            throw new \Exception("Não localizado parâmetros para esse municipio.");
        }
        return (object)$urls[$cmun];
    }


    /**
     * SOAP communication dependency injection
     * @param SoapInterface $soap
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
    }

    /**
     * Build tag Prestador
     */
    protected function buildPrestadorTag()
    {
        $this->prestador = "<Prestador>"
            . "<Cnpj>" . $this->config->cnpj . "</Cnpj>"
            . "<InscricaoMunicipal>" . $this->config->im . "</InscricaoMunicipal>"
            . "</Prestador>";
    }

    /**
     * Sign XML passing in content
     * @param string $content
     * @param string $tagname
     * @param string $mark
     * @return string XML signed
     */
    public function sign($content, $tagname, $mark)
    {
        $xml = Signer::sign(
            $this->certificate,
            $content,
            $tagname,
            $mark
        );
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Send message to webservice
     * @param string $message
     * @param string $operation
     * @return string XML response from webservice
     */
    public function send($message, $operation)
    {
        $action = "{$this->wsobj->msgns}.$operation.$operation";
        $url = $this->wsobj->homologacao;
        if ($this->environment === 'producao') {
            $url = $this->wsobj->producao;
            $action = str_replace("www2", "www", $action);
        }
        if (empty($url)) {
            throw new \Exception("Não está registrada a URL para o ambiente "
                . "de {$this->environment} desse municipio.");
        }
        $url = sprintf("%s%s.CLS", $url, $operation);
        $request = $this->createSoapRequest($message, $operation);
        $this->lastRequest = $request;
        if (empty($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        $msgSize = strlen($request);
        $parameters = [
            "Content-Type: text/xml;charset=UTF-8",
            "SOAPAction: \"$action\"",
            "Content-length: $msgSize"
        ];
        $response = (string)$this->soap->send(
            $operation,
            $url,
            $action,
            $request,
            $parameters
        );
        return $this->extractContentFromResponse($response, $operation);
    }

    /**
     * Extract xml response from CDATA outputXML tag
     * @param string $response Return from webservice
     * @return string XML extracted from response
     */
    protected function extractContentFromResponse($response, $operation)
    {
        if (substr($response, 0, 1) !== '<') {
            return $response;
        }
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($response);
        if (!empty($dom->getElementsByTagName('outputXML')->item(0))) {
            $node = $dom->getElementsByTagName('outputXML')->item(0);
            return $node->textContent;
        }
        return $response;
    }

    /**
     * Build SOAP request
     * @param string $message
     * @param string $operation
     * @return string XML SOAP request
     */
    protected function createSoapRequest($message, $operation)
    {
        $env = "<soapenv:Envelope "
            . "xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">"
            . "<soapenv:Header/>"
            . "<soapenv:Body>"
            . "<$operation xmlns=\"{$this->wsobj->soapns}\">"
            . "$message"
            . "</$operation>"
            . "</soapenv:Body>"
            . "</soapenv:Envelope>";
        if ($this->environment === 'producao') {
            $env = str_replace("www2", "www", $env);
        }
        return $env;
    }

}
