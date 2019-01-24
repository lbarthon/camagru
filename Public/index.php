<?php

/**
 * Index that handles all requests.
 * The chdir allow to include everything from the project path.
 */

chdir('..');
require 'Core/Autoloader.php';
Autoloader::register();

use App\General\GeneralController;
use Core\Router;

$url = $_GET['path'];

/**
 * Router instanced and all routes added here.
 */

$router = new Router();

$router->route("", "App\General\GeneralController@index");
$router->route("index", "App\General\GeneralController@index");
$router->route("account", "App\General\GeneralController@myAccount");

$router->route("user/login", "App\Users\UsersController@login");
$router->route("user/create", "App\Users\UsersController@create");
$router->route("user/resetpw/(.*)", "App\Users\UsersController@resetpw");
$router->route("user/logout", "App\Users\UsersController@logout");

$router->route("setup", "Config\SetupController@setup");

/**
 * Router executed -> Prints the page to the user.
 * If false is returned, 404 error page is printed.
 */

$router->execute($url);
