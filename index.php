<?php

use CoffeeCode\Router\Router;

ob_start();

require __DIR__ . "/vendor/autoload.php";

$route = new Router(url());
$route->namespace("Source\App");

/**
 * Web Routes
 */
$route->group(null);
$route->get('/', 'Web:home', 'web.home');
$route->get('/sobre-nos', 'Web:about', 'web.about');
$route->get('/contactos', 'Web:contacts', 'web.contacts');
$route->get('/faqs', 'Web:faqs', 'web.faqs');
$route->get('/blog', 'Web:blog', 'web.blog');
$route->get('/casos-de-uso', 'Web:cases', 'web.cases');

/**
 * Errors
 */
$route->namespace('Source\App');
$route->group('ops');
$route->get('/{err_code}', 'Web:error', 'web.error');

/**
 * Dispatch
 */
$route->dispatch();


/**
 * Error
 */
if ($route->error()) {
    $route->redirect("/ops/{$route->error()}");
}

ob_end_flush();
