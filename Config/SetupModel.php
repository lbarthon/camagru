<?php

namespace Config;

use PDOException;
use Core\Model;
use Exceptions\SqlException;

class SetupModel extends Model {

    /**
     * Function that setups the MySQL db.
     */
    public function setup() {
        require 'Config/Database.php';
        try {
            $this->init_no_db(true);
        } catch (SqlException $e) {
            $this->setFlash('setup_error', $e->getMessage());
            return;
        }
        try {
            self::$_conn->exec("CREATE DATABASE " . explode("=", explode(";", $db_dsn)[0])[1]);
        } catch (PDOException $e) {
            $this->setFlash('setup_error', $e->getMessage());
            return;
        }
        try {
            $this->init(true);
        } catch (SqlException $e) {
            $this->setFlash('setup_error', $e->getMessage());
            return;
        }
        $queries = [
            "CREATE TABLE users (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            pwd VARCHAR(128) NOT NULL,
            notifs BOOLEAN DEFAULT 1,
            conf_link VARCHAR(255),
            confirmed BIT DEFAULT 0
            )",
            "CREATE TABLE pictures (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            img TEXT NOT NULL,
            id_user INT(10) UNSIGNED NOT NULL,
            FOREIGN KEY (id_user) REFERENCES users(id)
            )",
            "CREATE TABLE resetpw (
            id_user INT(10) UNSIGNED NOT NULL,
            FOREIGN KEY (id_user) REFERENCES users(id),
            uniqueid VARCHAR(255) NOT NULL,
            `date` INT(30) NOT NULL
            )",
            "CREATE TABLE comments (
            id INT(15) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            id_picture INT(10) UNSIGNED NOT NULL,
            id_user INT(10) UNSIGNED NOT NULL,
            FOREIGN KEY (id_picture) REFERENCES pictures(id),
            FOREIGN KEY (id_user) REFERENCES users(id),
            comment TEXT NOT NULL
            )",
            "CREATE TABLE likes (
            id_picture INT(10) UNSIGNED NOT NULL,
            id_user INT(10) UNSIGNED NOT NULL,
            FOREIGN KEY (id_picture) REFERENCES pictures(id),
            FOREIGN KEY (id_user) REFERENCES users(id)
            )"
        ];
        foreach ($queries as $query) {
            try {
                self::$_conn->exec($query);
            } catch (PDOException $e) {
                $this->setFlash('setup_error', "Erreur lors de la conncetion à la base de données."
                    . "<br>" . $e->getMessage());
                return;
            }
        }
        if (!file_exists('Public/Pictures/Users')) {
            mkdir('Public/Pictures/Users');
        }
    }
}
