<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TDBConnection
 *
 * the class behavior like an object
 * but we can have only one class (connectionection) at time per client
 * 
 * @author erivelton
 */
class TDBConnection {
    /*
     * conecction itself
     * dump
     */

    private static $connection;

    /*
     *  sql stament
     */
    private static $stament;

    /*
     *  last id after insert
     */
    private static $lastId;

    /*
     * block new instances for this class
     */

    private function __construct() {
        
    }

    /*
     * Open the ado conenction 
     */

    private static function open() {

        // set variables from Config.php
        $host = DBHOST;
        $user = DBUSRNAME;
        $pass = DBUSRPASSWORD;
        $name = DBNAME;
        $port = DBPORT;

        // set domain server name from Config.php
        $dsn = "mysql:host={$host};port={$port};dbname={$name}";

        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );
        // Create a new PDO instanace
        try {
            self::$connection = new PDO($dsn, $user, $pass, $options);
        }
        // Catch any errors and stop rum
        catch (PDOException $e) {
            die("Conexão ao [{$host}] db : [dbname={$name}] não pode ser estabelecida: " . $e->getMessage());
        }
    }

    /*
     * get : if null start a new connectionection
     */

    public static function getConnection() {

        if (!(self::$connection)) {
            self::open();
        }

        return self::$connection;
    }

    /*
     * The prepare function allows you to bind values into your SQL statements. 
     * This is important because it takes away the threat of SQL Injection because 
     * you are no longer having to manually include the parameters into the query string.
     * Using the prepare function will also improve performance when running the 
     * same query with different parameters multiple times.
     */

    public static function prepareQuery($query) {
        self::$stament = self::$connection->prepare($query);
    }

    /*
     * Bind a param to a sql stament :param_name, valor, null
     * You need to prepare query before
     */

    public static function bindParamQuery($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        // bind if has a stament
        if (self::$stament) {
            self::$stament->bindValue($param, $value, $type);
        }
    }

    /*
     * Execute
     * for insert, update, delete
     */

    public static function execute() {
        return self::$stament->execute();
    }

    /*
     * return a array of objects from database
     * fos select
     */

    public static function resultset() {
        self::execute();
        return self::$stament->fetchAll(PDO::FETCH_OBJ);
    }

    /*
     * return a single object from database
     */

    public static function single() {
        self::execute();
        return self::$stament->fetch(PDO::FETCH_OBJ);
    }

    /*
     * return the row count from a stament
     */

    public static function rowCount() {
        return self::$stament->rowCount();
    }

    /*
     * return lst inserted id
     */

    public static function lastInsertId() {
        return self::$connection->lastInsertId();
    }

    /*
     * To begin a transaction
     */

    public static function beginTransaction() {
        return self::$connection->beginTransaction();
    }

    /*
     * To end a transaction and commit your changes
     */

    public static function endTransaction() {
        return self::$connection->commit();
    }

    /*
     * To cancel a transaction and roll back your changes
     */

    public static function cancelTransaction() {
        return self::$connection->rollBack();
    }
    
    /*
     * The Debut Dump Parameters methods dumps the the information that was 
     * contained in the Prepared Statement
     */
    
    public static function debugDumpParams() {
        return self::$stament->debugDumpParams();
    }

}

// end of class TDBConnection
?>
