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

    public function __construct($url = "") {
        $this->_url = $url;
    }

    public function redirect($loc) {
        header("Location: " . $loc);
        die();
    }

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
        require $this->_templatePath . str_replace('.', '/', $this->_template) . '.php';
    }
}
