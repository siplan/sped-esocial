<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../../../bootstrap.php';

use JsonSchema\Constraints\Constraint;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

//versão S_1.3.0

$evento  = 'evtInfoEmpregador';
$version = 'S_01_03_00';

$jsonSchema = '{
    "title": "evtInfoEmpregador",
    "type": "object",
    "properties": {
        "sequencial": {
            "required": false,
            "type": ["integer","null"],
            "minimum": 1,
            "maximum": 99999
        },
        "modo": {
            "required": true,
            "type": "string",
            "$ref": "#/definitions/modo"
        },
        "ideperiodo": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "inivalid": {
                    "required": true,
                    "type": "string",
                    "$ref": "#/definitions/periodo"
                },
                "fimvalid": {
                    "required": false,
                    "type": ["string","null"],
                    "$ref": "#/definitions/periodo"
                }
            }
        },
        "infocadastro": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "classtrib": {
                    "required": true,
                    "type": "string",
                    "maxLength": 2
                },
                "indcoop": {
                    "required": false,
                    "type": ["integer","null"],
                    "minimum": 0,
                    "maximum": 3
                },
                "indconstr": {
                    "required": false,
                    "type": ["integer","null"],
                    "minimum": 0,
                    "maximum": 1
                },
                "inddesfolha": {
                    "required": true,
                    "type": "integer",
                    "minimum": 0,
                    "maximum": 2
                },
                "indopccp": {
                    "required": false,
                    "type": ["integer","null"],
                    "minimum": 1,
                    "maximum": 2
                },
                "indporte": {
                    "required": false,
                    "type": ["string","null"],
                    "pattern": "^(S)$"
                },
                "indoptregeletron": {
                    "required": true,
                    "type": "integer",
                    "minimum": 0,
                    "maximum": 1
                },
                "cnpjefr": {
                    "required": false,
                    "type": ["string","null"],
                    "pattern": "^[0-9]{14}$"
                },
                "dttrans11096": {
                    "required": false,
                    "type": ["string","null"],
                    "$ref": "#/definitions/data"
                },
                "indtribfolhapispasep": {
                    "required": false,
                    "type": ["string","null"],
                    "pattern": "^(S)$"
                }
            }
        },
        "dadosisencao": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "ideminlei": {
                    "required": true,
                    "type": "string",
                    "maxLength": 70
                },
                "nrcertif": {
                    "required": true,
                    "type": "string",
                    "maxLength": 40
                },
                "dtemiscertif": {
                    "required": true,
                    "type": "string",
                    "$ref": "#/definitions/data"
                },
                "dtvenccertif": {
                    "required": true,
                    "type": "string",
                    "$ref": "#/definitions/data"
                },
                "nrprotrenov": {
                    "required": false,
                    "type": ["string","null"],
                    "maxLength": 40
                },
                "dtprotrenov": {
                    "required": false,
                    "type": ["string","null"],
                    "$ref": "#/definitions/data"
                },
                "dtdou": {
                    "required": false,
                    "type": ["string","null"],
                    "$ref": "#/definitions/data"
                },
                "pagdou": {
                    "required": false,
                    "type": ["integer","null"],
                    "maximum": 99999
                }
            }
        },
        "infoorginternacional": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "indacordoisenmulta": {
                    "required": true,
                    "type": "integer",
                    "minimum": 0,
                    "maximum": 1
                }
            }
        },
        "novavalidade": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "inivalid": {
                    "required": true,
                    "type": "string",
                    "$ref": "#/definitions/periodo"
                },
                "fimvalid": {
                    "required": false,
                    "type": ["string","null"],
                    "$ref": "#/definitions/periodo"
                }
            }
        }
    }
}';

//campos OBRIGATORIOS
$std = new \stdClass();
$std->sequencial = 1; //numero sequencial
$std->modo = 'INC'; //INC inclusão, ALT alteração EXC exclusão

$std->ideperiodo = new \stdClass();
$std->ideperiodo->inivalid = '2017-01'; //aaaa-mm do inicio da validade
$std->ideperiodo->fimvalid = '2017-07'; //yyyy-mm do fim da validade

//campo OPCIONAL
//usar em Inclusão ou alteração apenas
$std->infocadastro = new \stdClass();
$std->infocadastro->classtrib = '01'; // classificação tributária do contribuinte, conforme tabela 8
$std->infocadastro->natjurid = '2062'; //código da Natureza Jurídica do Contribuinte, conforme tabela 21
$std->infocadastro->indcoop = 0;//Indicativo de Cooperativa: 0 - Não é cooperativa; 1 - Cooperativa de Trabalho; 2 - Cooperativa de Produção; 3 - Outras Cooperativas.
$std->infocadastro->indconstr = 0;//Indicativo de Construtora: 0 - Não é Construtora; 1 - Empresa Construtora.
$std->infocadastro->inddesfolha = 0; //Indicativo de Desoneração da Folha: 0 - Não Aplicável; 1 - Empresa enquadrada nos art. 7º a 9º da Lei 12.546/2011.
$std->infocadastro->indopccp = 2; //Indicativo da opção pelo produtor rural pela forma de tributação da contribuição previdenciária, nos termos do art. 25, §13, da Lei 8.212/1991 e do art. 25, §7°, da Lei 8.870/1994. O não preenchimento deste campo por parte do produtor rural implica opção pela comercialização da sua produção: 1 - Sobre a comercialização da sua produção; 2 - Sobre a folha de pagamento.
$std->infocadastro->indporte = 'S'; //Indicativo de microempresa ou empresa de pequeno porte
$std->infocadastro->indoptregeletron = 0; //registro eletrônico de empregados: 0 - Não optou pelo registro eletrônico de empregados; 1 - Optou pelo registro eletrônico de empregados
$std->infocadastro->cnpjefr = '12345678901234'; //Opcional
$std->infocadastro->dttrans11096 = '2023-01-22'; //Opcional
//Data da transformação em sociedade de fins lucrativos - Lei 11.096/2005
//Não preencher se {classTrib}(./classTrib) = [21, 22].
$std->infocadastro->indtribfolhapispasep = 'S'; //Opcional
//Indicador de tributação sobre a folha de pagamento - PIS e COFINS.
//Preenchimento exclusivo para o empregador em situação de tributação de PIS e COFINS sobre a folha de pagamento.

$std->infocadastro->indented = 'N';//realiza a contratação de aprendiz por entidade N - Não é entidade educativa sem fins lucrativos; S - É entidade educativa sem fins lucrativos
$std->infocadastro->indett = 'N';//Indicativo de Empresa de Trabalho Temporário N - Não é Empresa de Trabalho Temporário; S - Empresa de Trabalho Temporário.
$std->infocadastro->nrregett = null;//Número do registro da Empresa de Trabalho Temporário

//campo OPCIONAL
//Informações Complementares - Empresas Isentas - Dados da Isenção
//usar esses campos somente de declarar infocadastro
$std->dadosisencao = new \stdClass();
$std->dadosisencao->ideminlei = 'seila';//Sigla e nome do Ministério ou Lei que concedeu o Certificado
$std->dadosisencao->nrcertif = '987654321';//Número do Certificado de Entidade Beneficente de Assistência Social, número da portaria de concessão do Certificado, ou, no caso de concessão através de Lei específica, o número da Lei.
$std->dadosisencao->dtemiscertif = '2016-11-04';//Data de Emissão do Certificado/publicação da Lei
$std->dadosisencao->dtvenccertif = '2018-11-03';//Data de Vencimento do Certificado
$std->dadosisencao->nrprotrenov = null;//Protocolo pedido renovação
$std->dadosisencao->dtprotrenov = null;//Data do protocolo de renovação
$std->dadosisencao->dtdou = null;//Preencher com a data de publicação no Diário Oficial da União
$std->dadosisencao->pagdou = null;// número da página no DOU referente à publicação do documento de concessão do certificado.

//campo OPCIONAL
//Informações exclusivas de organismos internacionais e outras instituições extraterritoriais
$std->infoorginternacional = new \stdClass();
$std->infoorginternacional->indacordoisenmulta = 0; //Indicativo da existência de acordo internacional para isenção de multa: 0 - Sem acordo; 1 - Com acordo.

//campo OPCIONAL
//Informação preenchida exclusivamente em caso de alteração do período de validade das informações do registro identificado no evento, apresentando o novo período de validade.
//usar somente em caso de alteração
$std->novavalidade = new \stdClass();
$std->novavalidade->inivalid = '2017-06';//mês e ano de início da validade das informações prestadas no evento,
$std->novavalidade->fimvalid = null;//mês e ano de término da validade das informações, se houve

// Schema must be decoded before it can be used for validation
$jsonSchemaObject = json_decode($jsonSchema);
if (empty($jsonSchemaObject)) {
    echo "<h2>Erro de digitação no schema ! Revise</h2>";
    echo "<pre>";
    print_r($jsonSchema);
    echo "</pre>";
    die();
}

// The SchemaStorage can resolve references, loading additional schemas from file as needed, etc.
$schemaStorage = new SchemaStorage();

// This does two things:
// 1) Mutates $jsonSchemaObject to normalize the references (to file://mySchema#/definitions/integerData, etc)
// 2) Tells $schemaStorage that references to file://mySchema... should be resolved by looking in $jsonSchemaObject
$definitions = realpath(__DIR__."/../../../jsonSchemes/definitions.schema");
$schemaStorage->addSchema("file:{$definitions}", $jsonSchemaObject);

// Provide $schemaStorage to the Validator so that references can be resolved during validation
$jsonValidator = new Validator(new Factory($schemaStorage));

// Do validation (use isValid() and getErrors() to check the result)
$jsonValidator->validate(
    $std,
    $jsonSchemaObject,
    Constraint::CHECK_MODE_COERCE_TYPES  //tenta converter o dado no tipo indicado no schema
);

if ($jsonValidator->isValid()) {
    echo "The supplied JSON validates against the schema.<br/>";
} else {
    echo "JSON does not validate. Violations:<br/>";
    foreach ($jsonValidator->getErrors() as $error) {
        echo sprintf("[%s] %s<br/>", $error['property'], $error['message']);
    }
    die;
}
//salva se sucesso
file_put_contents("../../../jsonSchemes/v_$version/$evento.schema", $jsonSchema);
