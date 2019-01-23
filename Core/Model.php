<?php

namespace Core;

/**
 * Model abstract class
 */

use PDO;
use PDOException;
use Exceptions\SqlException;

abstract class Model {
    protected static $_conn;

    protected function init_no_db($force = false) {
        require 'Config/Database.php';
        if ($force) {
            self::$_conn = null;
        }
        else if (self::$_conn) {
            return self::$_conn;
        }
        try {
            $host = 'mysql:' . explode(";", $db_dsn)[1];
            self::$_conn = new PDO($host, $db_user, $db_password);
        } catch (PDOException $e) {
            throw new SqlException("Erreur lors de la conncetion à la base de données."
                . "<br>" . $e->getMessage(), $e->getCode());
        }
    }

    protected function init($force = false) {
        require 'Config/Database.php';
        if ($force) {
            self::$_conn = null;
        }
        else if (self::$_conn) {
            return self::$_conn;
        }
        try {
            self::$_conn = new PDO($db_dsn, $db_user, $db_password);
        } catch (PDOException $e) {
            throw new SqlException("Erreur lors de la conncetion à la base de données."
                . "<br>" . $e->getMessage(), $e->getCode());
        }
    }
}
