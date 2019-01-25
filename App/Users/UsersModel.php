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
        $conf_link = bin2hex(random_bytes(50));
        $stmt = self::$_conn->prepare("INSERT INTO users (username, email, pwd, conf_link) VALUES (?,?,?,?)");
        $stmt->execute([$username, $email, $pwd, $conf_link]);
        mail($email, "Confirmation du compte",
            "Voici le lien pour Confirmer votre mot de passe :" .
            "\nhttps://" . $_SERVER['HTTP_HOST'] . "/user/confirm/" . $conf_link .
            "\n\nÀ bientôt sur Camagru!",
            "From: camagru@barthonet.ovh\r\n");
        $this->setFlash('create_success', "Compte créé avec succès!<br>" .
            "Veuillez maintenant le confirmer grace au lien que vous avez reçu par mail!<br>" .
            "Si vous n'avez rien reçu, regardez vos spams!");
        return true;
    }

    /**
     * Function called that'll confirm the user account so he'll be able to log.
     */
    public function confirm($url) {
        $url = htmlspecialchars(str_replace('user/confirm/', '', $url));
        try {
            $this->init();
        } catch (SqlException $e) {
            return false;
        }
        try {
            $stmt = self::$_conn->prepare("SELECT id FROM users WHERE conf_link = ?");
            $stmt->execute([$url]);
            $match = $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
        if (!isset($match) || empty($match)) {
            return false;
        }
        $uid = $match['id'];
        try {
            $stmt = self::$_conn->prepare("UPDATE users SET conf_link=NULL,confirmed=1 WHERE id=?");
            $stmt->execute([$uid]);
        } catch (PDOException $e) {
            return false;
        }
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
            $stmt = self::$_conn->prepare("SELECT id_user,`date` FROM resetpw WHERE uniqueid = ?");
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
     * Functions that gets the user id from the mail sent.
     */
    public function getUserIdFromMail($mail) {
        try {
            $this->init();
        } catch (SqlException $e) {
            return -1;
        }
        try {
            $stmt = self::$_conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$mail]);
            $match = $stmt->fetch();
        } catch (PDOException $e) {
            return -1;
        }
        if (!isset($match) || empty($match)) {
            return -1;
        }
        return $match['id'];
    }

    /**
     * Function that sends a mail to $mail with the link to reset his password.
     */
    public function createReset($mail) {
        $mail = htmlspecialchars($mail);
        $uid = $this->getUserIdFromMail($mail);
        if ($uid !== -1) {
            $uniqueid = bin2hex(random_bytes(50));
            try {
                $this->init();
            } catch (SqlException $e) {
                return false;
            }
            try {
                $stmt = self::$_conn->prepare("INSERT INTO resetpw (id_user, uniqueid, `date`) VALUES (?,?,?)");
                $stmt->execute([$uid, $uniqueid, time()]);
            } catch (PDOException $e) {
                return false;
            }
            mail($mail, "Réinitialisation du mot de passe",
                "Voici le lien pour réinitialiser votre mot de passe :\n" .
                "https://" . $_SERVER['HTTP_HOST'] . "/user/resetpw/" . $uniqueid .
                "\n\nÀ bientôt sur Camagru!",
                "From: camagru@barthonet.ovh\r\n");
            return true;
        }
        return false;
    }

    /**
     * Functions called to reset pw.
     * It compares the input mail (check) with the flash one we took before.
     * Returns false if reset went wrong, and false otherwise.
     */
    public function resetPw($email, $newpw) {
        $uid = $this->getFlash('reset_id');
        $real_email = $this->getFlash('reset_email');
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
            $newpw = hash("whirlpool", $newpw);
            $stmt = self::$_conn->prepare("UPDATE users SET pwd=? WHERE id=?");
            $stmt->execute([$newpw, $uid]);
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

    /**
     * Check password safety.
     */
    public function isPwdSafe($pwd) {
        $upper = preg_match('#[A-Z]#', $pwd);
        $lower = preg_match('#[a-z]#', $pwd);
        $nbr = preg_match('#[\d]#', $pwd);
        $special = preg_match('#[^a-zA-Z\d]#', $pwd);
        $len = strlen($pwd);
        return ($upper >= 1 && $lower >= 1 && $nbr >= 1 && $special >= 1 && $len >= 8);
    }

    /**
     * Function that update members informations.
     * If pwd isn't specified, won't be updated
     */
    public function update($username, $email, $notifs, $pwd = null) {
        $old_username = $_SESSION['user'];
        // TODO -- Check if username or mail not being used
        // TODO -- Notifs update (rn won't)
        try {
            $this->init();
        } catch (SqlException $e) {
            $this->setFlash('edit_err', 'Erreur lors de la mise à jour de votre profil.');
        }
        try {
            if ($pwd !== null) {
                $pwd = hash("whirlpool", $pwd);
                $stmt = self::$_conn->prepare("UPDATE users SET username=?,email=?,notifs=?,pwd=? WHERE username=?");
                $stmt->execute([$username, $email, $notifs, $pwd, $old_username]);
            } else {
                $stmt = self::$_conn->prepare("UPDATE users SET username=?,email=?,notifs=? WHERE username=?");
                $stmt->execute([$username, $email, $notifs, $old_username]);
            }
            $_SESSION['user'] = $username;
        } catch (PDOException $e) {
            $this->setFlash('edit_err', 'Erreur lors de la mise à jour de votre profil.');
        }
        $this->setFlash('edit_success', 'Profil mis à jour avec succès!');
    }
}
