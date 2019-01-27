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
            return false;
        }
        try {
            $stmt = self::$_conn->prepare("SELECT notifs FROM users WHERE username=?");
            $stmt->execute([$username]);
            $match = $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
        if (!isset($match) || empty($match)) {
            return false;
        }
        return $match['notifs'] === '1' ? true : false;
    }

    /**
     * Function that returns an array containing pictures of the asked page.
     * $page is the asked page informations.
     * 5 pictures per page.
     */
    public function getPageInfos(int $page) {
        try {
            $this->init();
        } catch (SqlException $e) {
            return null;
        }
        try {
            $stmt = self::$_conn->prepare(
                "SELECT img, username, (SELECT COUNT(*) FROM likes WHERE likes.id_picture = pictures.id) AS likes FROM pictures INNER JOIN users ON pictures.id_user = users.id ORDER BY pictures.id DESC LIMIT " . $page * 5 . ",5"
            );
            $stmt->execute();
            $matches = $stmt->fetchAll();
            return $matches;
        } catch (PDOException $e) {
            return null;
        }
    }
}
