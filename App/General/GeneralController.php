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
     * Function that prints the page tha tallows the user to post a picture.
     */
    public function montage() {
        if ($this->_model->islogged()) {
            $this->render("General.Montage");
        } else {
            $this->redirect("/account");
        }
    }

    /**
     * Fucntion that's called in post when the user posts the picture.
     */
    public function add_pic() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['add_pic']) && !empty($_POST['add_pic'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if ($this->_model->addPicture($_POST['picture'])) {
                    $this->_model->setFlash("add_success", "Photo ajoutée avec succès!");
                    $this->redirect("/add");
                }
            }
        }
        $this->_model->setFlash("add_error", "Erreur lors de l'ajout de votre photo!");
        $this->redirect("/add");
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

    /**
     * Like page, must be called with ajax.
     */
    public function like() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['like']) && !empty($_POST['like'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if (!$this->_model->like(explode("/", $this->_url)[1])) {
                    echo "error";
                }
            }
        }
    }

    /**
     * Dislike page, must be called with ajax.
     */
    public function dislike() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['dislike']) && !empty($_POST['dislike'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if (!$this->_model->dislike(explode("/", $this->_url)[1])) {
                    echo "error";
                }
            }
        }
    }
}
