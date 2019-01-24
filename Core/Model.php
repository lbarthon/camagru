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

    /**
     * Globally init the static sql connection (no db specified).
     * If $force = true, it'll overrides and delete the old one.
     * SqlException thrown on error.
     */
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

    /**
     * Init the static sql connection.
     * If $force = true, it'll overrides and delete the old one.
     * SqlException thrown on error.
     */
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

    /**
     * Compare form token and session token, to avoid CSRF.
     */
    public function compareTokens($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['token']) && !empty($_SESSION['token'])) {
            if ($_SESSION['token'] === $token) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a session flash variable and deletes it.
     */
    public function getFlash($name) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash']) && !empty($_SESSION['flash'])) {
            if (isset($_SESSION['flash'][$name]) && !empty($_SESSION['flash'][$name])) {
                $var = $_SESSION['flash'][$name];
                unset($_SESSION['flash'][$name]);
                return $var;
            }
        }
        return "";
    }

    /**
     * Sets in the session values a flash variable.
     * When the getter will be used on it, it'll be destroyed.
     */
    public function setFlash($name, $flash) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['flash']) || empty($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][$name] = $flash;
    }
}
