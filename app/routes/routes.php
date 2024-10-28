<?php
use flight\Engine;
use flight\net\Router;


/**
 * Setup the 404
 */

Flight::map('notFound', function () use ($app) {
    $app->json('Resource not found', 404);
    
 });


/** 
 * @var Router $router 
 * @var Engine $app
 */
$router->get('/', function() use ($app) {
    $app->json('No resource provided',404);
});


//var_dump($app);



