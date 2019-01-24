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

    /**
     * Function that handles the setup form (1st part).
     * 2nd part just basically prints the page (2nd part).
     */
    public function setup() {
        if (isset($_POST) && !empty($_POST)) {
            $this->_model->setup();
            $error = $this->_model->getFlash('setup_error');
            if ($error) {
                $this->render("Setup", compact('error'));
            } else {
                $this->redirect("/index");
            }
        } else {
            $error = "";
            $this->render("Setup", compact('error'));
        }
    }
}
