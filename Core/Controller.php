<?php

namespace Core;

/**
 * Controller abstract class
 */

abstract class Controller {
    protected $_url;
    protected $_viewPath;
    protected $_templatePath;
    protected $_template;
    protected $_model;

    /**
     * Constructor, sets the url called by the user, to use $_GET.
     */
    public function __construct($url = "") {
        $this->_url = $url;
    }

    /**
     * Function that redirects to an other page using header.
     * Just dies next;
     */
    public function redirect($loc) {
        header("Location: " . $loc);
        die();
    }

    /**
     * Render function that prints the view using the specified template.
     * Added : Prints a popup if there's one in flash vars.
     */
    public function render($file, $values = []) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $token = bin2hex(random_bytes(50));
        $_SESSION['token'] = $token;
        ob_start();
        extract($values);
        require $this->_viewPath . str_replace('.', '/', $file) . '.php';
        $content = ob_get_clean();
        $_logged = isset($_SESSION['user']) ? true : false;
        if (!isset($js)) $js = null;
        if (isset($this->_model) && $this->_model !== null) {
            $var = $this->_model->getFlash('popup');
            if (isset($var) && !empty($var) && $var !== null && $var !== "") {
                $js .= '<script>window.onload = function() {alert("' . $var . '");};</script>';
            }
        }
        require $this->_templatePath . str_replace('.', '/', $this->_template) . '.php';
    }
}
