<?php

use CoffeeCode\Router\Router;

ob_start();

require __DIR__ . "/vendor/autoload.php";

$route = new Router(url());
$route->namespace("Source\Controllers");

/**
 * API Routes
 */
$route->group('api');

// Auth
$route->post('/login', 'Access:login');
$route->post('/register', 'Access:register');

// Billets
$route->post('/boleto/create', 'Billets:create');

// Payers
$route->post('/pagador/create', 'Payers:create');
$route->get('/pagador/me', 'Payers:all');
$route->post('/pagador/delete/{payer_id}', 'Payers:delete');
$route->post('/pagador/update/{payer_id}', 'Payers:update');

// Users
$route->post('/me', 'Users:me');

/**
 * Dispatch
 */
$route->dispatch();


/**
 * Error Endpoint
 */
if ($route->error()) {
    header('Content-Type: application/json;charset=utf8');
    header('HTTP/1.1 404 Not Found');

    $json = [
        "success" => false,
        "message" => "Endpoint not found"
    ];

    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return;
}

ob_end_flush();
