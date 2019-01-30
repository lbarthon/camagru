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
     * Returns the user id from the session var 'user'
     */
    public function getUserIdFromSessionUsername() {
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
            $stmt = self::$_conn->prepare("SELECT id FROM users WHERE username=?");
            $stmt->execute([$username]);
            $match = $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
        if (!isset($match) || empty($match)) {
            return false;
        }
        return $match['id'];
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
                "SELECT pictures.id, img, username, (SELECT COUNT(*) FROM likes WHERE likes.id_picture = pictures.id) AS likes FROM pictures" .
                " INNER JOIN users ON pictures.id_user = users.id ORDER BY pictures.id DESC LIMIT " . $page * 5 . ",5"
            );
            $stmt->execute();
            $matches = $stmt->fetchAll();
            return $matches;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getNbrPictures() {
        try {
            $this->init();
        } catch (SqlException $e) {
            return -1;
        }
        try {
            $stmt = self::$_conn->prepare("SELECT id FROM pictures");
            $stmt->execute();
            $nbr = $stmt->rowCount();
        } catch (PDOException $e) {
            return -1;
        }
        return $nbr;
    }

    /**
     * Function called to add a picture to camagru.
     * Returns true if success, false otherwise.
     */
    public function addPicture($picture) {
        $picture = substr($picture, 22);
        try {
            $this->init();
        } catch (SqlException $e) {
            return false;
        }
        $path = "/Pictures/Users/" . bin2hex(random_bytes(50)) . ".png";
        file_put_contents("./Public" . $path, base64_decode($picture));
        $user_id = $this->getUserIdFromSessionUsername();
        if (!$user_id) {
            return false;
        }
        try {
            $stmt = self::$_conn->prepare("INSERT INTO pictures (img, id_user) VALUES (?, ?)");
            $stmt->execute(["." . $path, $user_id]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Like function.
     * Likes the picture as the session user if he hasn't already liked.
     */
    public function like($picture_id) {
        if (!$this->isLogged()) return false;
        try {
            $this->init();
        } catch (SqlException $e) {
            return false;
        }
        try {
            $user_id = $this->getUserIdFromSessionUsername();
            $stmt = self::$_conn->prepare("SELECT * FROM likes WHERE id_user=? AND id_picture=?");
            $stmt->execute([$user_id, $picture_id]);
            if ($stmt->rowCount() > 0) return false;
            $stmt = self::$_conn->prepare("INSERT INTO likes (id_user, id_picture) VALUES (?,?)");
            $stmt->execute([$user_id, $picture_id]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Dislike function.
     * Dislikes the picture as the session user if he has liked it.
     */
    public function dislike($picture_id) {
        if (!$this->isLogged()) return false;
        try {
            $this->init();
        } catch (SqlException $e) {
            return false;
        }
        try {
            $user_id = $this->getUserIdFromSessionUsername();
            $stmt = self::$_conn->prepare("SELECT * FROM likes WHERE id_user=?");
            $stmt->execute([$user_id]);
            if ($stmt->rowCount() === 0) return false;
            $stmt = self::$_conn->prepare("DELETE FROM likes WHERE id_user=? AND id_picture=?");
            $stmt->execute([$user_id, $picture_id]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Function that adds a comment!
     */
    public function comment($picture_id, $comment) {
        if (!$this->isLogged() || $comment === null) return false;
        try {
            $this->init();
        } catch (SqlException $e) {
            return false;
        }
        try {
            $user_id = $this->getUserIdFromSessionUsername();
            $stmt = self::$_conn->prepare("INSERT INTO comments (id_user, id_picture, comment) VALUES (?,?,?)");
            $stmt->execute([$user_id, $picture_id, $comment]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }
}
