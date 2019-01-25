<?php

namespace App\General;

use \PDO;
use Core\Model;
use Exceptions\SqlException;

class GeneralModel extends Model {

    /**
     * Basically a function that checks in session if user is logged.
     */
    public function isLogged() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            return true;
        }
        return false;
    }

    /**
     * Get user's mail from username.
     */
    public function getMailFromSessionUsername() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $username = $_SESSION['user'];
        try {
            $this->init();
        } catch (SqlException $e) {
            return "";
        }
        try {
            $stmt = self::$_conn->prepare("SELECT email FROM users WHERE username=?");
            $stmt->execute([$username]);
            $match = $stmt->fetch();
        } catch (PDOException $e) {
            return "";
        }
        if (!isset($match) || empty($match)) {
            return "";
        }
        return $match['email'];
    }

    /**
     * Returns 0 or 1 if user has mail notifications enabled, -1 otherwise.
     */
    public function getNotifsFromSessionUsername() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $username = $_SESSION['user'];
        try {
            $this->init();
        } catch (SqlException $e) {
            return -1;
        }
        try {
            $stmt = self::$_conn->prepare("SELECT notifs FROM users WHERE username=?");
            $stmt->execute([$username]);
            $match = $stmt->fetch();
        } catch (PDOException $e) {
            return -1;
        }
        if (!isset($match) || empty($match)) {
            return -1;
        }
        return $match['notifs'];
    }
}
