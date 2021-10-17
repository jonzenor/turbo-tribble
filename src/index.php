<?php
require './vendor/autoload.php';

// Enable this section to show errors in development
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

// Enable this section to hide errors in production
// $app = new \Slim\App;

$container = $app->getContainer();

/**
 * Container Definitions
 */

$container['db'] = function($c) {
	$database = $user = $password = "sakila";
	$host = "mysql";

	$db = new PDO("mysql:host={$host};dbname={$database};charset=utf8", $user, $password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
};

$container['movieData'] = function($c) {
	return new \DataLayer\MovieData($c['db']);
};

$container['categoryData'] = function($c) {
	return new \DataLayer\CategoryData($c['db']);
};


/**
 * Routes
 */

$app->get('/movies', \Controllers\MovieController::class . ':listAll');
$app->get('/movie/{id}', \Controllers\MovieController::class . ':listMovie');
$app->get('/search/{term}', \Controllers\MovieController::class . ':searchTitle');
$app->get('/rated/{rating}', \Controllers\MovieController::class . ':searchRating');
$app->get('/category/{name}', \Controllers\MovieController::class . ':searchCategory');

$app->run();
