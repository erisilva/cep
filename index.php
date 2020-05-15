<?php
    /*
     * constantes com a configuração da conexão com o banco de dados
     */
    require_once 'config/config.php';

    /*
     * cabeçalho da página
     */
    header('Content-Type: text/html; charset=utf-8');
    ini_set("date.timezone", 'America/Sao_Paulo');
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__FILE__) . '/log/log.txt');
    ini_set('error_reporting', E_ALL ^ E_NOTICE);

    /*
     * conexão
     */
    $dsn = "mysql:host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME;

    $options = array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );
    
    // cria a conexão
    try {
        $connection = new PDO($dsn, DBUSRNAME, DBUSRPASSWORD, $options);
    }
    catch (PDOException $e) {
        die("Conexão ao [{".DBHOST."}] db : [dbname={".DBNAME."}] não pode ser estabelecida: " . $e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        # variavel value, pode ser o cep ou logradouro
        $value = filter_input(INPUT_GET, 'value', FILTER_SANITIZE_SPECIAL_CHARS);       
        // elimina qualquer coisa que não seja numeros    
        if (isset($value) && !empty($value)) {
            $value = strip_tags($value);
            $value = preg_replace("/[^0-9]/", "", $value);
        } else {
            $erro["value"] = "Parâmetro incorreto. Campo value precisa ser um cep.";
        }
        // o cep filtrado e tratado precisa ser formado por 8 digitos
        if (strlen($value) != 8){
            $erro["value"] = "Parâmetro incorreto. Campo value precisa ter 8 digitos numericos.";
        }

        # variavel method de saída do script: json ou xml
        $method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_SPECIAL_CHARS);    
        if (isset($method) && !empty($method)) {
            $method = trim($method);
            $method = strip_tags($method);
        } else {
            $method = "json"; // default caso não tenha sido passado
        }
        // só aceita a variável method como sendo json ou xml
        if (($method != 'json') and ($method != 'xml')){
            die("Parâmetro incorreto. O parametro method só pode ser xml ou json.");    
        }

        $query = $connection->prepare("SELECT * FROM cep WHERE cep = :cep;");
        $query->bindValue(':cep', $value, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
 
        if (!empty($result)) {
            $endereco = array(
                'cep' => $result->cep,
                'cidade' => $result->cidade,
                'rua' => $result->rua,
                'bairro' => $result->bairro,
                'uf' => $result->uf,
            );
            $resultado[] = $endereco;
        } else {
            $erro["cep"] = "cep não encontrado.";
        }

        if ($method == 'xml'){
            $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><consulta_cep></consulta_cep>");
            if (!isset($erro)) {
                foreach ($resultado as $resultadoItem){
                    $enderecoChild = $xml->addChild('endereco');
                    $enderecoChild->addChild('cep', $resultadoItem["cep"]);
                    $enderecoChild->addChild('cidade', $resultadoItem["cidade"]);
                    $enderecoChild->addChild('rua', $resultadoItem["rua"]);
                    $enderecoChild->addChild('bairro', $resultadoItem["bairro"]);
                    $enderecoChild->addChild('uf', $resultadoItem["uf"]);
                }
                Header('Content-type: text/xml');
                print($xml->asXML());
            } else { // end of !isset($erro)
                $erroChild = $xml->addChild('erro');
                $erroChild->addChild('erro', 'erro');
                foreach ($erro as $erroItem => $erroItemNome) {
                    $erroChild = $xml->addChild('erro');
                    $erroChild->addChild($erroItem, $erroItemNome);
                }
                Header('Content-type: text/xml');
                print($xml->asXML());
            } // end of !isset($erro)
        } // end of $method == 'xml'

        if ($method == 'json'){
            if (!isset($erro)) {
                $myJSON = json_encode($resultado, JSON_UNESCAPED_UNICODE);
                header("Content-type: application/json; charset=utf-8");
                print($myJSON);
            } else {
                $myJSON = json_encode(array_merge(['erro' => 'erro'], $erro), JSON_UNESCAPED_UNICODE);
                header("Content-type: application/json; charset=utf-8");
                print($myJSON);
            } // end of !isset($erro)
        } // end of $method == 'json'
    }

// echo "<pre>\n";
// print_r($resultado);
// echo "</pre>\n";

// echo "<pre>\n";
// print_r($erro);
// echo "</pre>\n";