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
$setup = false;

/**
 * Router instanced and all routes added here.
 */

$router = new Router();

$router->route("", "App\General\GeneralController@index");
$router->route("index", "App\General\GeneralController@index");
$router->route("page/(\d*)", "App\General\GeneralController@index");
$router->route("account", "App\General\GeneralController@myAccount");
$router->route("add", "App\General\GeneralController@add");
$router->route("add_pic", "App\General\GeneralController@add_pic");
$router->route("like/(\d*)", "App\General\GeneralController@like");
$router->route("dislike/(\d*)", "App\General\GeneralController@dislike");

$router->route("user/login", "App\Users\UsersController@login");
$router->route("user/create", "App\Users\UsersController@create");
$router->route("user/edit", "App\Users\UsersController@edit");
$router->route("user/resetpw_ask", "App\Users\UsersController@resetpw_ask");
$router->route("user/resetpw/(.*)", "App\Users\UsersController@resetpw");
$router->route("user/confirm/(.*)", "App\Users\UsersController@confirm");
$router->route("user/logout", "App\Users\UsersController@logout");

if (!$setup) {
    $router->route("setup", "Config\SetupController@setup");
}
/**
 * Router executed -> Prints the page to the user.
 * If false is returned, 404 error page is printed.
 */

$router->execute($url);
