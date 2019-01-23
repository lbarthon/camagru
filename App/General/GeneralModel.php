<?php

namespace App\General;

use Core\Model;

class GeneralModel extends Model {

    public function isLogged() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
            return true;
        }
        return false;
    }
}
