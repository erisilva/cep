<?php
/*
 * constants
 */
require_once 'config/TConfig.php';

/*
 *  autoload
 */
require_once 'libs/Autoloader.php';
$loader = new Autoloader();
$loader->directories = array('libs');
$loader->register();

/*
 * header page
 */
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/log/log.txt');
ini_set('error_reporting', E_ALL ^ E_NOTICE);

/*
 * se conecta
 */
TDBConnection::getConnection();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

} // end of if (method = GET)

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    # variavel value, pode ser o cep ou logradouro
    $_GET['value'] = trim($_GET['value']);
    if (isset($_GET['value']) && !empty($_GET['value'])) {
        $value = strip_tags($_GET['value']);
    } else {
        $erro["value"] = "Parâmetro incorreto. Campo value precisa ter um valor.";
    }

    # variavel method, pode variar: json, qwerty
    $_GET['method'] = trim($_GET['method']);
    if (isset($_GET['method']) && !empty($_GET['method'])) {
        $method = strip_tags($_GET['method']);

    } else {
        $erro["method"] = "Parâmetro incorreto. Campo method precisa ter um valor.";
    }

    # variavel debug, deve ser qualquer coisa, nula ou vazia
    # se tiver algum valor faz o debug ficar true
    $_GET['debug'] = strip_tags(trim($_GET['debug']));
    if (isset($_GET['debug']) && !empty($_GET['debug'])) {
        $debug = true;
    } else {
        $debug = false;
    }

    # variavel field, precisa ser cep ou log de logradouro
    $_GET['field'] = strip_tags(trim($_GET['field']));
    if (isset($_GET['field']) && !empty($_GET['field'])) {
        $field = $_GET['field'];
        if ( !in_array($field, ['cep', 'log'])){
            $erro["field"] = "field de value inválida";
        }
    } else {
        $erro["field"] = "Parâmetro incorreto. Campo field precisa ter um valor.";
    }

    // consulta por cep
    if ($field == 'cep') {
        $cep = preg_replace("/[^0-9]/", "", $value);
        if (strlen($cep) == 8){
            $cep3 = substr($cep, -3);
            $cep5 = substr($cep, 0, 5);
          if ($cep3 == "000"){
                TDBConnection::prepareQuery("SELECT * FROM cep_unico WHERE cep = :cep;");
                TDBConnection::bindParamQuery(':cep', $cep5 . "-" . $cep3, PDO::PARAM_STR);
                $cep_unico = TDBConnection::single();
                if (!empty($cep_unico)) {
                    $endereco = array(
                        'cep' => $cep5 . "-" . $cep3,
                        'cidade' => $cep_unico->Nome,
                        'logradouro' => '',
                        'bairro' => '',
                        'uf' => $cep_unico->UF,
                        'tipo' => '',
                    );
                    $resultado[] = $endereco;
                } else {
                    $erro["cep"] = "cep (único) não encontrado.";
                }
            } else { /* logradoures completos */
                // achar o estado
                TDBConnection::prepareQuery("SELECT uf FROM cep_log_index where cep5 = :cep;");
                TDBConnection::bindParamQuery(':cep', $cep5, PDO::PARAM_STR);
                $encontrarUfCep = TDBConnection::single();
                if (!empty($encontrarUfCep)){
                    TDBConnection::prepareQuery("SELECT * FROM " . $encontrarUfCep->uf . " where cep = :cep;");
                    TDBConnection::bindParamQuery(':cep', $cep5 . "-" . $cep3, PDO::PARAM_STR);
                    $encontrarEnderecoCep = TDBConnection::single();
                    $endereco = array(
                        'cep' => $cep5 . "-" . $cep3,
                        'cidade' => $encontrarEnderecoCep->cidade,
                        'logradouro' => $encontrarEnderecoCep->logradouro,
                        'bairro' => $encontrarEnderecoCep->bairro,
                        'uf' => $encontrarUfCep->uf,
                        'tipo' => $encontrarEnderecoCep->tp_logradouro,
                    );
                    $resultado[] = $endereco;
                } else {
                    $erro["cep"] = "cep não encontrado, uf inválido p\ $cep5.";
                }
            }
        } else {
            $erro["cep"] = "cep inválido, formato inválido";
        }

        if ($method == 'xml'){
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><consulta_cep></consulta_cep>");
            if (!isset($erro)) {
                foreach ($resultado as $resultadoItem){
                    $enderecoChild = $xml->addChild('endereco');
                    $enderecoChild->addChild('cep', $resultadoItem["cep"]);
                    $enderecoChild->addChild('cidade', $resultadoItem["cidade"]);
                    $enderecoChild->addChild('logradouro', $resultadoItem["logradouro"]);
                    $enderecoChild->addChild('bairro', $resultadoItem["bairro"]);
                    $enderecoChild->addChild('uf', $resultadoItem["uf"]);
                    $enderecoChild->addChild('tipo', $resultadoItem["tipo"]);
                }
                Header('Content-type: text/xml');
                print($xml->asXML());
            } else { // end of !isset($erro)
                if ($debug) {
                    foreach ($erro as $erroItem => $erroItemNome) {
                        $erroChild = $xml->addChild('erro');
                        $erroChild->addChild($erroItem, $erroItemNome);
                    }
                    Header('Content-type: text/xml');
                    print($xml->asXML());
                } else {
                    $erroChild = $xml->addChild('erro');
                    $erroChild->addChild('erro', 'erro');
                    Header('Content-type: text/xml');
                    print($xml->asXML());
                }
            } // end of !isset($erro)
        } // end of $method == 'xml'

        if ($method == 'json'){
            if (!isset($erro)) {
                $myJSON = json_encode($resultado, JSON_UNESCAPED_UNICODE);
                header("Content-type: application/json; charset=utf-8");
                print($myJSON);
            } else {
                if ($debug) {
                    $myJSON = json_encode($erro, JSON_UNESCAPED_UNICODE);
                    header("Content-type: application/json; charset=utf-8");
                    print($myJSON);
                } else {
                    $myJSON = json_encode(['erro' => 'erro']);
                    header("Content-type: application/json; charset=utf-8");
                    print($myJSON);
                }
            } // end of !isset($erro)
        } // end of $method == 'json'
    } // end of if ($field == 'cep')



} // end of if (method = GET)


// testes meu amor

//
//echo "<pre>\n";
//print_r($erro);
//echo "</pre>\n";

