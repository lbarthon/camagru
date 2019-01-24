<?php

namespace Config;

use Core\Controller;
use Config\SetupModel;

class SetupController extends Controller {

    public function __construct($url) {
        parent::__construct($url);
        $this->_model = new SetupModel();
        $this->_viewPath = 'Config/';
        $this->_templatePath = 'App/Templates/';
        $this->_template = 'General.General';
    }

    public function setup() {
        $error = "";
        if (isset($_POST) && !empty($_POST)) {
            $error = $this->_model->setup();
            if ($error) {
                $this->render("Setup", compact('error'));
            } else {
                $this->redirect("/index");
            }
        } else {
            $this->render("Setup", compact('error'));
        }
    }
}
