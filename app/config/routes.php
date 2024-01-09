<?php

use app\controllers\ApiExampleController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */
$router->get('/', function() use ($app) {
	$app->render('welcome');
});

$router->group('/api', function() use ($router, $app) {
	$Api_Example_Controller = new ApiExampleController($app);
	$router->map('/users', [ $Api_Example_Controller, 'getUsers' ]);
	$router->map('/users/@id', [ $Api_Example_Controller, 'getUser' ]);
	$router->map('/users/@id', [ $Api_Example_Controller, 'updateUser' ]);
});