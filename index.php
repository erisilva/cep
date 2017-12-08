<?php
/*
 * inicia a sessão
 */
session_start();

/*
 * constants
 */
require_once 'config/TConfig.php';

/*
 *  autoload
 */
require_once 'libs/Autoloader.php';
$loader = new Autoloader();
$loader->directories = array('libs', 'model');
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

/*
 * segurança
 * para toda requisição é feita a criação de um token aleatorio
 * se não existir nenhum token de sessao ao se solicitar
 * a pagina é criado um token
 */
/*if (!isset($_SESSION['token'])){
    date_default_timezone_set('America/Sao_Paulo');
    $token = md5(uniqid(rand(), TRUE));
    $quando = date("Y-m-d H:i:s");
    $_SESSION['token'] = $token;
    $_SESSION['token_time'] = time();

    /* captura o e-mail da origem da requisição dessa página */
    //$ip = gethostbyaddr($_SERVER['REMOTE_ADDR']);

    /* Grava o logacesso com o ip origem da requisição, juntamente com token com a data/hora */
    /* ERRO MEU: eu chamei o campo de data/hora de description em vez de quando como uso. Vou arrumar algum dia.*/
/*
    TDBConnection::beginTransaction();
    TDBConnection::prepareQuery("INSERT INTO logacesso VALUES (null, :token, :ip, :description);");
    TDBConnection::bindParamQuery(':token', $token, PDO::PARAM_STR);
    TDBConnection::bindParamQuery(':ip', $ip, PDO::PARAM_STR);
    TDBConnection::bindParamQuery(':description', $quando, PDO::PARAM_STR);
    TDBConnection::execute();
    TDBConnection::endTransaction();
}

precisarei de três variáveis:

field ->cep
     ->loc
value

method
*/


if ($_SERVER["REQUEST_METHOD"] == "POST") {

} // end of if (method = GET)

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    # variavel value, pode ser o cep ou logradouro
    $_GET['value'] = trim($_GET['value']);
    if (isset($_GET['value']) && !empty($_GET['value'])) {
        $value = strip_tags($_GET['value']);
    } else {
        $erro["value"] = "[erro: Termo de value vazio]";
    }

    # variavel method, pode variar: json, qwerty
    $_GET['method'] = trim($_GET['method']);
    if (isset($_GET['method']) && !empty($_GET['method'])) {
        $method = strip_tags($_GET['method']);

    } else {
        $erro["method"] = "[erro: method de value não definida]";
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
            $erro["field"] = "[erro: field de value inválida]";
        }
    } else {
        $erro["field"] = "[erro: field de value não definida]";
    }

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
                    $erro["cep"] = "[erro: cep único não encontrado.]";
                }

            } else /* logradoures completos */ {
                // achar o estado
                TDBConnection::prepareQuery("SELECT uf FROM cep_log_index where cep5 = :cep;");
                TDBConnection::bindParamQuery(':cep', $cep5, PDO::PARAM_STR);
                $estadoCEP5 = TDBConnection::single();
                if (!empty($estadoCEP5)){
                    TDBConnection::prepareQuery("SELECT * FROM " . $estadoCEP5->uf . " where cep = :cep;");
                    TDBConnection::bindParamQuery(':cep', $cep5 . "-" . $cep3, PDO::PARAM_STR);
                    $cep_completo = TDBConnection::single();
                    $endereco = array(
                        'cep' => $cep5 . "-" . $cep3,
                        'cidade' => $cep_completo->cidade,
                        'logradouro' => $cep_completo->logradouro,
                        'bairro' => $cep_completo->bairro,
                        'uf' => $estadoCEP5->uf,
                        'tipo' => $cep_completo->tp_logradouro,
                    );
                    $resultado[] = $endereco;
                } else {
                    $erro["cep"] = "[erro: cep não encontrado, uf inválido p\ $cep5.]";
                }
            }
        } else {
            $erro["cep"] = "[erro: cep inválido, formato inválido]";
        }
    } // end of if ($field == 'cep')
} // end of if (method = GET)


// testes meu amor
echo "<pre>\n";
print_r($resultado);
echo "</pre>\n";

echo "<pre>\n";
print_r($cep_unico);
echo "</pre>\n";

echo "<pre>\n";
print_r($method);
echo "</pre>\n";

echo "<pre>\n";
print_r( $estadoCEP5) ;
echo "</pre>\n";

echo "<pre>\n";
print_r($erro);
echo "</pre>\n";

