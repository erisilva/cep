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

tipo ->cep
     ->loc
busca

metodo
*/


if ($_SERVER["REQUEST_METHOD"] == "POST") {

} // end of if (method = GET)

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    # variavel tipo, precisa ser cep ou loc
    $_GET['tipo'] = trim($_GET['tipo']);
    if (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
        $tipo = strip_tags($_GET['tipo']);

    } else {
        $erro["tipo"] = "[erro: Tipo de busca não definida]";
    }

    # variavel tipo, precisa ser cep ou loc
    $_GET['busca'] = trim($_GET['busca']);
    if (isset($_GET['busca']) && !empty($_GET['busca'])) {
        $busca = strip_tags($_GET['busca']);

    } else {
        $erro["busca"] = "[erro: Termo de busca vazio]";
    }

    # variavel tipo, precisa ser cep ou loc
    $_GET['metodo'] = trim($_GET['metodo']);
    if (isset($_GET['metodo']) && !empty($_GET['metodo'])) {
        $metodo = strip_tags($_GET['metodo']);

    } else {
        $erro["metodo"] = "[erro: metodo de busca não definida]";
    }

} // end of if (method = GET)


// testes meu amor
echo "<pre>\n";
print_r($tipo);
echo "</pre>\n";

echo "<pre>\n";
print_r($busca);
echo "</pre>\n";

echo "<pre>\n";
print_r($metodo);
echo "</pre>\n";

echo "<pre>\n";
print_r($erro);
echo "</pre>\n";

