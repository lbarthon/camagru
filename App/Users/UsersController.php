<?php

namespace App\Users;

use Core\Controller;
use Exceptions\SqlException;
use App\Users\UsersModel;

class UsersController extends Controller {

    public function __construct($url) {
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
                if ($this->_model->isPwdSafe($_POST['password'])) {
                    $this->_model->addUser($_POST['username'], $_POST['mail'], $_POST['password']);
                } else {
                    $this->_model->setFlash('create_err', "Votre mot de passe n'est pas suffisamment sécurisé!");
                }
                $this->redirect("/account");
            }
        }
        $this->redirect("/index");
    }

    /**
     * Email confirmation called here.
     */
    public function confirm() {
        if ($this->_model->confirm($this->_url)) {
            $this->_model->setFlash('popup', 'Email confirmé avec succès!\nVeuillez maintenant vous connecter.');
        } else {
            $this->_model->setFlash('popup', 'Ce lien est inconnu!');
        }
        $this->redirect('/account');
    }

    /**
     * Function called when an user edits his profile
     */
    public function edit() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['edit']) && !empty($_POST['edit'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                $notifs = isset($_POST['notifs']) === true ? 1 : 0;
                if (isset($_POST['password'])) {
                    if ($this->_model->isPwdSafe($_POST['password'])) {
                        $this->_model->update($_POST['username'], $_POST['email'], $notifs, $_POST['password']);
                    } else {
                        $this->_model->setFlash('edit_err', "Votre mot de passe n'est pas suffisamment sécurisé!");
                    }
                } else {
                    $this->_model->update($_POST['username'], $_POST['email'], $notifs);
                }
            }
        }
        $this->redirect("/account");
    }

    /**
     * Function that's called when the user asks for a new password.
     */
    public function resetpw_ask() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['reset']) && !empty($_POST['reset'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if ($this->_model->createReset($_POST['mail'])) {
                    $this->_model->setFlash('popup', 'Mail envoyé!');
                } else {
                    $this->_model->setFlash('popup', 'Erreur lors de l\'envoi du mail!\nAvez vous un compte?');
                }
            }
        }
        $this->redirect("/account");
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
        die($user_id);
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
            }
        }
        $this->redirect("/index");
    }
}
