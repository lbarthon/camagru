<?php

namespace App\General;

use Core\Controller;
use Exceptions\SqlException;
use App\General\GeneralModel;

class GeneralController extends Controller {

    public function __construct($url) {
        parent::__construct($url);
        $this->_model = new GeneralModel();
        $this->_viewPath = 'App/Views/';
        $this->_templatePath = 'App/Templates/';
        $this->_template = 'General.General';
    }

    /**
     * Function that renders the index page.
     */
    public function index() {
        $url = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras mauris neque, blandit sit amet convallis.";
        $this->render('General.Index', compact('url'));
    }

    /**
     * Function that renders the account page if the user is logged.
     * If he isn't, it'll render the login page.
     */
    public function myAccount() {
        if ($this->_model->isLogged()) {
            $this->render('General.Account');
        }
        else {
            $this->login();
        }
    }

    /**
     * Function that renders the login page.
     */
    public function login() {
        $login_err = "<p class='flash_err'>" . $this->_model->getFlash('login_err') . "</p>";
        $create_err = "<p class='flash_err'>" . $this->_model->getFlash('create_err') . "</p>";
        $create_success = "<p class='flash_success'>" . $this->_model->getFlash('create_success') . "</p>";
        $this->render('General.Login', compact('login_err', 'create_err', 'create_success'));
    }
}
