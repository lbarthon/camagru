<?php

namespace App\Users;

use Core\Controller;
use Exceptions\SqlException;
use App\Users\UsersModel;

class UsersController extends Controller {

    public function __construct($url = "") {
        parent::__construct($url);
        $this->_model = new UsersModel();
    }

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
    
    public function create() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['create']) && !empty($_POST['create'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                $this->_model->addUser($_POST['username'], $_POST['mail'], $_POST['password']);
                $this->redirect("/account");
            }
        }
        $this->redirect("/index");
    }

    public function resetpw() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['resetpw']) && !empty($_POST['resetpw'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
            
            }
        }
        $this->redirect("/index");
    }

    public function logout() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['logout']) && !empty($_POST['logout'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                session_destroy();
            }
        }
        $this->redirect("/index");
    }
}
