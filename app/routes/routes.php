<?php
use flight\Engine;
use flight\net\Router;


 $request = Flight::request();
 $origin = $request->getVar('HTTP_ORIGIN');
 

// Set CORS headers before any route handling
Flight::before('start', function(&$params, &$output) {
    header("Access-Control-Allow-Origin: *"); // Allows all origins
    // Or specify a specific origin:
    // header("Access-Control-Allow-Origin: https://your-frontend-domain.com");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allowed HTTP methods
    header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allowed headers
    // Optional: Allow credentials (cookies, HTTP authentication)
    // header("Access-Control-Allow-Credentials: true");

    // Handle preflight OPTIONS requests
    if (Flight::request()->method === 'OPTIONS') {
        exit(); // Terminate the script after sending CORS headers for OPTIONS
    }
});


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



