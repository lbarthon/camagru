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
            $matches = $this->_model->getSessionUserPictures();
            $add_success = "<p class='flash_success'>" . $this->_model->getFlash('add_success') . "</p>";
            $add_error = "<p class='flash_err'>" . $this->_model->getFlash('add_error') . "</p>";
            $this->render("General.Montage", compact('matches', 'add_success', 'add_error'));
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
                    $this->redirect("/montage");
                }
            }
        }
        $this->_model->setFlash("add_error", "Erreur lors de l'ajout de votre photo!");
        $this->redirect("/montage");
    }

    /**
     * Function that renders one picture.
     */
    public function picture() {
        $picture = $this->_model->getPicture(explode("/", $this->_url)[1]);
        if ($picture === null) {
            $this->redirect("/index");
        }
        $this->render("General.Picture", compact('picture'));
    }

    /**
     * Function that renders the asked page.
     */
    public function page(int $page) {
        $matches = $this->_model->getPageInfos($page);
        if ($matches) {
            $total = $this->_model->getNbrPictures();
            if ($total === -1) {
                $prevpage = false;
                $nextpage = false;
                $this->render("General.Page", compact('matches', 'nextpage', 'prevpage'));
            } else {
                $prevpage = $page !== 0;
                $nextpage = ($page + 1) * 5 < $total;
                $this->render("General.Page", compact('matches', 'nextpage', 'prevpage', 'page'));
            }
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
     * Deletes the picture in the url.
     * Method called in post.
     */
    public function delete() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['delete']) && !empty($_POST['delete'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if (!$this->_model->delete(explode("/", $this->_url)[1])) {
                    echo "error";
                }
            }
        }
    }

    /**
     * Like page, must be called using ajax.
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
     * Dislike page, must be called using ajax.
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

    /**
     * Comment page, must be called using ajax.
     */
    public function comment() {
        if (isset($_POST) && !empty($_POST) && isset($_POST['comment']) && !empty($_POST['comment'])) {
            if ($this->_model->compareTokens($_POST['token'])) {
                if (!$this->_model->comment(explode("/", $this->_url)[1], $_POST['comment'])) {
                    echo "error";
                }
            }
        }
    }
}
