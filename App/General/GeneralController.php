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
     * It calls the page function with the asked page
     */
    public function index() {
        $exploded = explode("/", $this->_url);
        if (count($exploded) > 1) {
            $this->page(intval($exploded[1]));
        } else {
            $this->page(0);
        }
    }

    /**
     * Function that renders the asked page.
     */
    public function page(int $page) {
        $matches = $this->_model->getPageInfos($page);
        if ($matches) {
            $this->render("General.Page", compact('matches'));
        } else if ($page === 0){
            $this->render("General.Void");
        } else {
            $this->page(0);
        }
    }

    /**
     * Function that renders the account page if the user is logged.
     * If he isn't, it'll render the login page.
     */
    public function myAccount() {
        if ($this->_model->isLogged()) {
            $username = $_SESSION['user'];
            $email = $this->_model->getMailFromSessionUsername();
            $checked = $this->_model->getNotifsFromSessionUsername();
            $edit_success = "<p class='flash_success'>" . $this->_model->getFlash('edit_success') . "</p>";
            $edit_err = "<p class='flash_err'>" . $this->_model->getFlash('edit_err') . "</p>";
            $this->render('General.Account', compact('username', 'email', 'edit_success', 'edit_err', 'checked'));
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
