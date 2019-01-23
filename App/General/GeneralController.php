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

    public function index() {
        $url = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras mauris neque, blandit sit amet convallis.";
        $this->render('General.Index', compact('url'));
    }

    public function myAccount() {
        if ($this->_model->isLogged()) {
            $this->render('General.Account');
        }
        else {
            $this->login();
        }
    }

    public function login() {
        $this->render('General.Login');
    }
}
