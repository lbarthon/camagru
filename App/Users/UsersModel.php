<?php

namespace App\Users;

use \PDO;
use Core\Model;
use Exceptions\SqlException;

class UsersModel extends Model {

    /**
     * Function that tells if the specified user password is good or not.
     * Warning : the var $pwd is the password before encryption.
     */
    public function auth($username, $pwd) {
        $username = htmlspecialchars($username);
        $pwd = hash("whirlpool", $pwd);
        try {
            $this->init();
        } catch (SqlException $e) {
            $this->setFlash('login_err', "Erreur lors de la connection à la base de données. Veuillez contacter un administrateur.");
            return false;
        }
        $stmt = self::$_conn->prepare("SELECT pwd FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $match = $stmt->fetch();
        if (isset($match) && !empty($match)) {
            if ($match['pwd'] === $pwd) {
                return true;
            }
        }
        $this->setFlash('login_err', "Nom d'utilisateur ou mot de passe incorrect!");
        return false;
    }

    /**
     * Function that adds an user.
     * Warning : the var $pwd is the password before encryption.
     */
    public function addUser($username, $email, $pwd) {
        $username = htmlspecialchars($username);
        $email = htmlspecialchars($email);
        $pwd = hash("whirlpool", $pwd);
        try {
            $this->init();
        } catch (SqlException $e) {
            $this->setFlash('create_err', "Erreur lors de la connection à la base de données. Veuillez contacter un administrateur.");
            return false;
        }
        $stmt = self::$_conn->prepare("SELECT pwd FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $match = $stmt->fetch();
        if (isset($match) && !empty($match)) {
            $this->setFlash('create_err', "Mail ou nom d'utilisateur déjà utilisé!");
            return false;
        }
        $stmt = self::$_conn->prepare("INSERT INTO users (username, email, pwd) VALUES (?,?,?)");
        $stmt->execute([$username, $email, $pwd]);
        $this->setFlash('create_success', "Compte créé avec succès!");
        return false;
    }
}
