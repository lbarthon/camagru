<?php
/**
 * Index that handles all requests.
 * The chdir allow to include everything from the project path.
 */
chdir('..');
/**
 * Loading autoloader :)
 */
require 'Core/Autoloader.php';
Autoloader::register();

use App\General\GeneralController;
use Core\Router;

$url = $_GET['path'];
/**
 * Setup variable, that add setup mvc to all the routes.
 * Set to true to disable setup page access.
 */
$setup = true;
/**
 * Router instanced and all routes added here.
 */
$router = new Router();
/**
 * Those 2 pages print index, equivalent to page/0
 */
$router->route("", "App\General\GeneralController@index");
$router->route("index", "App\General\GeneralController@index");
/**
 * Prints the asked page or picture.
 */
$router->route("page/(\d*)", "App\General\GeneralController@index");
$router->route("picture/(\d*)", "App\General\GeneralController@picture");
/**
 * Account page :)
 */
$router->route("account", "App\General\GeneralController@myAccount");
/**
 * Montage page, where the user can see all the pictures he took.
 * He'll be able to delete them here.
 */
$router->route("montage", "App\General\GeneralController@montage");
/**
 * Post forms to add or delete a picture.
 */
$router->route("add_pic", "App\General\GeneralController@add_pic");
$router->route("delete/(\d*)", "App\General\GeneralController@delete");
/**
 * Post forms that are called using ajax.
 */
$router->route("like/(\d*)", "App\General\GeneralController@like");
$router->route("dislike/(\d*)", "App\General\GeneralController@dislike");
$router->route("comment/(\d*)", "App\General\GeneralController@comment");
/**
 * User management forms, except resetpw.
 */
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
