# sped-nfse-tinus

Api para comunicação com webservices do [Provedor Tinus](https://www.tinus.com.br/)

## Esta API esta sendo testada para Parnamirim - RN

*Utilize o [forum NFePHP](https://groups.google.com/forum/#!forum/nfephp) iniciar discussões especificas sobre o desenvolvimento deste pacote.*


[![Latest Stable Version][ico-stable]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![License][ico-license]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

[![Issues][ico-issues]][link-issues]
[![Forks][ico-forks]][link-forks]
[![Stars][ico-stars]][link-stars]

Este pacote é aderente com os [PSR-1], [PSR-2] e [PSR-4]. Se você observar negligências de conformidade, por favor envie um patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

Não deixe de se cadastrar no [grupo de discussão do NFePHP](http://groups.google.com/group/nfephp) para acompanhar o desenvolvimento e participar das discussões e tirar duvidas!

## Municípios atendidos por esse provedor

|n|Município|UF|IBGE|
|:---:|:---|:---:|:---:|
|1|Cabedelo|PB|2503209|
|2|Cabo de Santo Agostinho|PE|2602902|
|3|Camaragibe|PE|2603454|
|4|Camaragibe|PE|2603454|
|5|Jaboatão dos Guararapes|PE|2607901|
|6|Macaiba|PE|2407104|
|7|Olinda|PE|2609600|
|8|Mossoró|RN|2408003|
|9|Parnamirim|RN|2403251|
|10|Rio do Fogo|RN|2408953|
|11|São Goncalo|RN|2412005|
|12|Tibau do Sul|RN|2414209|



## Dependências

- PHP >= 7.1
- ext-curl
- ext-soap
- ext-zlib
- ext-dom
- ext-openssl
- ext-json
- ext-simplexml
- ext-libxml

### Outras Libs

- nfephp-org/sped-common
- justinrainbow/json-schema

## Contribuindo
Este é um projeto totalmente *OpenSource*, para usa-lo e modifica-lo você não paga absolutamente nada. Porém para continuarmos a mante-lo é necessário qua alguma contribuição seja feita, seja auxiliando na codificação, na documentação ou na realização de testes e identificação de falhas e BUGs.

**Este pacote esta listado no [Packgist](https://packagist.org/) foi desenvolvido para uso do [Composer](https://getcomposer.org/), portanto não será explicitada nenhuma alternativa de instalação.**

*Durante a fase de desenvolvimento e testes este pacote deve ser instalado com:*
```bash
composer require nfephp-org/sped-nfse-tinus:dev-master
```

*Ou ainda,*
```bash
composer require nfephp-org/sped-nfse-tinus:dev-master --prefer-dist
```

*Ou ainda alterando o composer.json do seu aplicativo inserindo:*
```json
"require": {
    "nfephp-org/sped-nfse-tinus" : "dev-master"
}
```

> NOTA: Ao utilizar este pacote ainda na fase de desenvolvimento não se esqueça de alterar o composer.json da sua aplicação para aceitar pacotes em desenvolvimento, alterando a propriedade "minimum-stability" de "stable" para "dev".
> ```json
> "minimum-stability": "dev",
> "prefer-stable": true
> ```

*Após os stable realeases estarem disponíveis, este pacote poderá ser instalado com:*
```bash
composer require nfephp-org/sped-nfse-tinus
```
Ou ainda alterando o composer.json do seu aplicativo inserindo:
```json
"require": {
    "nfephp-org/sped-nfse-tinus" : "^1.0"
}
```

## Comentários

O mesmo segue a definição sugerida pela Abrasf, modelo 1.0, no entanto apresenta algumas particularidades:

– Não permite Substituição de RPS.

– O número do RPS deverá ser sequencial independente da série.

– É obrigatório informar dados para o Tomador.

1. Cancelamento NFS-e

Notas Fiscais de Serviço Eletrônica canceladas não retornam o arquivo de xml de NFS-e com as informações que a mesma foi cancelada. O padrão Tinus não permite consultar documentos cancelados.

2. Considerações

Observação 1: “Para o Município de Jaboatão dos Guararapes/PE, é necessário realizar o pedido de liberação de uso para o ambiente de Produção e homologação. Sem esta liberação, o RPS enviado ficará com status pendente no InvoiCy.

Para que a liberação seja realizada, o Município exige que seja enviado ao menos um RPS em homologação. Após envio deste RPS em homologação, o ERP receberá no retorno uma mensagem contendo o número do lote e o número do protocolo que aquele envio originou. Ex: Lote 12 enviado para processamento na prefeitura retornou protocolo 20131085123.

Este número de lote e protocolo, deve ser enviado ao e-mail jneilton@gmail.com, com o Assunto “Liberação de emissão de NFS-e em Homologação– Município X, CNPJ xx.xxx.xxx/xxxx-xx”. O Sr. Neilton, fará então a validação do lote, e a liberação do RPS enviado.

Após a validação do RPS enviado em homologação, será liberada a emissão em produção, pelo próprio Sr. Neilton.”

> Observação 2: “O município de Jaboatão dos Guararapes/PE, possui processamento assíncrono, ou seja, recebe os RPS e os processa posteriormente. O tempo médio para retorno do processamento de um RPS é de até 30 minutos. Neste tempo, o ERP deverá disparar algumas consultas, até obter o status final do RPS.”. Todos os Lotes carregados são processados a cada meia-hora do relógio Ex. 16:30, 17:00, 17:30, 18:00, 18:30, etc.

## Forma de uso
vide a pasta *Examples*

## Log de mudanças e versões
Acompanhe o [CHANGELOG](CHANGELOG.md) para maiores informações sobre as alterações recentes.

## Testing

Todos os testes são desenvolvidos para operar com o PHPUNIT

## Security

Caso você encontre algum problema relativo a segurança, por favor envie um email diretamente aos mantenedores do pacote ao invés de abrir um ISSUE.

## Credits

Cleiton Perin (owner and developer)

## Agradecimentos

Agradecemos o suporte oferecido pelo provedor TINUS para os ajustes nesta biblioteca.
É um caso muito raro de colaboração dentre todos os provedores de serviços para as prefeituras que habitualmente não fornecem qualquer tipo de auxilio para a integração com pacotes OpenSource.

## License

Este pacote está diponibilizado sob LGPLv3 ou MIT License (MIT). Leia  [Arquivo de Licença](LICENSE.md) para maiores informações.


[ico-stable]: https://poser.pugx.org/nfephp-org/sped-nfse-tinus/version
[ico-stars]: https://img.shields.io/github/stars/nfephp-org/sped-nfse-tinus.svg?style=flat-square
[ico-forks]: https://img.shields.io/github/forks/nfephp-org/sped-nfse-tinus.svg?style=flat-square
[ico-issues]: https://img.shields.io/github/issues/nfephp-org/sped-nfse-tinus.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/nfephp-org/sped-nfse-tinus/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/nfephp-org/sped-nfse-tinus.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/nfephp-org/sped-nfse-tinus.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nfephp-org/sped-nfse-tinus.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/nfephp-org/sped-nfse-tinus.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square
[ico-gitter]: https://img.shields.io/badge/GITTER-4%20users%20online-green.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/nfephp-org/sped-nfse-tinus
[link-travis]: https://travis-ci.org/nfephp-org/sped-nfse-tinus
[link-scrutinizer]: https://scrutinizer-ci.com/g/nfephp-org/sped-nfse-tinus/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/nfephp-org/sped-nfse-tinus
[link-downloads]: https://packagist.org/packages/nfephp-org/sped-nfse-tinus
[link-author]: https://github.com/nfephp-org
[link-issues]: https://github.com/nfephp-org/sped-nfse-tinus/issues
[link-forks]: https://github.com/nfephp-org/sped-nfse-tinus/network
[link-stars]: https://github.com/nfephp-org/sped-nfse-tinus/stargazers
[link-gitter]: https://gitter.im/nfephp-org/sped-nfse-tinus?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge