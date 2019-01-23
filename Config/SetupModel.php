<?php

namespace Config;

use PDOException;
use Core\Model;
use Exceptions\SqlException;

class SetupModel extends Model {

    public function setup() {
        require 'Config/Database.php';
        try {
            $this->init_no_db(true);
        } catch (SqlException $e) {
            return $e->getMessage();
        }
        self::$_conn->exec("CREATE DATABASE " . explode("=", explode(";", $db_dsn)[0])[1]);
        try {
            $this->init(true);
        } catch (SqlException $e) {
            return $e->getMessage();
        }
        $queries = [
            "CREATE TABLE users (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            pwd VARCHAR(128) NOT NULL   
            )",
            "CREATE TABLE pictures (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            img VARCHAR(255) NOT NULL,
            id_user INT(10) NOT NULL
            )",
            "CREATE TABLE resetpw (
            id_user INT(10) NOT NULL,
            uniqueid VARCHAR(50) NOT NULL
            )"
        ];
        foreach ($queries as $query) {
            try {
                self::$_conn->exec($query);
            } catch (PDOException $e) {
                return "Erreur lors de la conncetion à la base de données."
                    . "<br>" . $e->getMessage();
            }
        }
        return null;
    }
}
