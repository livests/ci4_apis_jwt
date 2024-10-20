<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//open routes
$routes->post("/auth/register", "Api\AuthorController::registerAuthor");
$routes->post("/auth/login", "Api\AuthorController::loginAuthor");
//prodected apis
$routes ->group("author", ["namespace" => "App\Controllers\Api", "filter" => "jwt"],function($routes){
    $routes->get("profile", "AuthorController::authorProfile");
    $routes->get("logout", "AuthorController::logoutAuthor");
    //books routes
    $routes->post("add-book", "BookController::createBook");
    $routes->get("list-book", "BookController::authorBooks");
    $routes->delete("delete-book/(:num)", "BookController::deleteAuthorBook/$1");
});