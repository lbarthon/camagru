<?php

namespace App\Users;

use Core\Controller;
use Exceptions\SqlException;
use App\Users\UsersModel;

class UsersController extends Controller {

    public function __construct($url = "") {
        parent::__construct($url);
        $this->_model = new UsersModel();
        $this->_viewPath = 'App/Views/';
        $this->_templatePath = 'App/Templates/';
        $this->_template = 'General.General';
    }

    /**
     * Login form called here.
     */
    public function login() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['login']) && !empty($_POST['login'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if ($this->_model->auth($_POST['username'], $_POST['password'])) {
                    $_SESSION['user'] = $_POST['username'];
                }
                $this->_model->setFlash('popup', 'Connecté avec succès!');
                $this->redirect("/account");
            }
        }
        $this->redirect("/index");
    }
    
    /**
     * Create form called here.
     */
    public function create() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['create']) && !empty($_POST['create'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                $this->_model->addUser($_POST['username'], $_POST['mail'], $_POST['password']);
                $this->redirect("/account");
            }
        }
        $this->_model->setFlash('popup', 'Compte crée avec succès!');
        $this->redirect("/index");
    }

    /**
     * Email confirmation called here.
     */
    public function confirm() {
        // TODO -- CONFIRMATION DE MAIL
    }

    /**
     * Reset password page rendered here (2nd part).
     * Form also called here (1st part).
     */
    public function resetpw() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['confirm']) && !empty($_POST['confirm'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if ($this->_model->resetPw($_POST['email'], $_POST['password'])) {
                    $this->_model->removeUrl($this->_url);
                    $this->_model->setFlash('popup', 'Mot de passe reset avec succès!');
                    $this->redirect("/account");
                } else {
                    $this->_model->setFlash('popup', 'Erreur lors de la réinitialisation de votre mot de passe!');
                }
            }
        }
        $user_id = $this->_model->getUserIdFromUrl($this->_url);
        if ($user_id === -2) {
            $this->_model->setFlash('popup', 'Votre lien a expiré!');
        } else if ($user_id !== -1) {
            $mail = $this->_model->getMailFromUserId($user_id);
            if (isset($mail) && $mail !== "" && $mail !== null) {
                $this->_model->setFlash('reset_email', $mail);
                $this->_model->setFlash('reset_id', $user_id);
                $url = $this->_url;
                $this->render('User.Resetpw', compact('url'));
                die();
            }
        }
        $this->_model->setFlash('popup', 'Ce lien est inconnu!');
        $this->redirect("/index");
    }

    /**
     * Logout form called here.
     */
    public function logout() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['logout']) && !empty($_POST['logout'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                session_destroy();
                $this->_model->setFlash('popup', 'Déconnecté avec succès!');
            }
        }
        $this->redirect("/index");
    }
}
