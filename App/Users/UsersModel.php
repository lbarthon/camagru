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
        $stmt = self::$_conn->prepare("SELECT pwd,confirmed FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $match = $stmt->fetch();
        if (isset($match) && !empty($match)) {
            if ($match['pwd'] === $pwd) {
                if ($match['confirmed'] === '0') {
                    $this->setFlash('login_err', "Votre compte n'a pas été confirmé!");
                    return false;
                }
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
        $sender = "mail@website.barthonet.ovh";
        // Not working -- To fix
        mail($email, "Confirm email", "HEY PD CONFIRME TON MAIL");
        // End of not working
        $this->setFlash('create_success', "Compte créé avec succès!<br>Veuillez maintenant le confirmer grace au lien que vous avez reçu par mail!");
        return true;
    }

    /**
     * Function that returns the id of the user that belongs to the UUID in the url.
     * Used for password reset form prompt, ...
     * It returns -2 if it was issued more than an hour ago.
     * Will return -1 if there's any error, and that'll redirect to index.
     */
    public function getUserIdFromUrl($url) {
        $url = htmlspecialchars(str_replace('user/resetpw/', '', $url));
        try {
            $this->init();
        } catch (SqlException $e) {
            return -1;
        }
        try {
            $stmt = self::$_conn->prepare("SELECT id_user FROM resetpw WHERE uniqueid = ?");
            $stmt->execute([$url]);
            $match = $stmt->fetch();
        } catch (PDOException $e) {
            return -1;
        }
        if (!isset($match) || empty($match)) {
            return -1;
        }
        if ($match['date'] + 3600 < time()) {
            $this->removeUrl($url);
            return -2;
        }
        return $match['id_user'];
    }

    /**
     * Functions that gets the mail from the user id.
     * The mail is used as a confirmation in the resetpw form.
     */
    public function getMailFromUserId($id) {
        try {
            $this->init();
        } catch (SqlException $e) {
            return "";
        }
        try {
            $stmt = self::$_conn->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$id]);
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
     * Functions that's called to reset pw.
     * It compares the input mail (check) with the flash one we took before.
     * Returns false if reset went wrong, and false otherwise.
     */
    public function resetPw($email, $newpw) {
        $uid = $this->getFlash('reset_id');
        $real_email = $this->getFlasg('reset_email');
        $email = htmlspecialchars($email);
        if ($email !== $real_email) {
            return false;
        }
        try {
            $this->init();
        } catch (SqlException $e) {
            return false;
        }
        try {
            $stmt = self::$_conn->prepare("UPDATE users SET pwd=? WHERE id=?");
            $stmt->execute([$newpw, $id]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Function that removes the url from the list of urls that allows to reset pw.
     */
    public function removeUrl($url) {
        $url = str_replace('user/resetpw/', '', $url);
        try {
            $this->init();
        } catch (SqlException $e) {
            return false;
        }
        try {
            $stmt = self::$_conn->prepare("DELETE FROM resetpw WHERE uniqueid = ?");
            $stmt->execute([$url]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }
}
