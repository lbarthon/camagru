<?php

chdir('..');
require 'Core/Autoloader.php';
Autoloader::register();

use App\General\GeneralController;
use Core\Router;

$url = $_GET['path'];

$router = new Router();

$router->route("", "App\General\GeneralController@index");
$router->route("index", "App\General\GeneralController@index");
$router->route("account", "App\General\GeneralController@myAccount");

$router->route("user/login", "App\Users\UsersController@login");
$router->route("user/create", "App\Users\UsersController@create");
$router->route("user/resetpw", "App\Users\UsersController@resetpw");
$router->route("user/logout", "App\Users\UsersController@logout");

$router->route("setup", "Config\SetupController@setup");

$ret = $router->execute($url);

if (!$ret) {
    header('HTTP/1.0 404 Not Found');
    echo "<h1 style='text-align:center;'>Error 404 - Page not found</h1>";
    echo '<h2>Debug -- URL = ' . $url . '</h2>';
}
    