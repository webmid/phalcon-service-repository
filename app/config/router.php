<?php
$router = new Phalcon\Mvc\Router();

//$router = $di->getRouter();

$router->add(
    '/user',
    [
        "controller" => "User",
        "action"     => "index",
    ]
)->setName('list-user');

$router->add(
    '/user/show/{id}',
    [
        "controller" => "User",
        "action"     => "show",
    ]
)->setName('show-user');

$router->add(
    '/user/add',
    [
        "controller" => "User",
        "action"     => "add",
    ]
)->setName('add-user');

$router->add(
    '/user/update',
    [
        "controller" => "User",
        "action"     => "update",
    ]
)->setName('update-user');

$router->add(
    '/user/delete',
    [
        "controller" => "User",
        "action"     => "delete",
    ]
)->setName('delete-user');


return $router->handle($_SERVER['REQUEST_URI']);
