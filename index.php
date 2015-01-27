<?php
require_once 'vendor/autoload.php';

// Initialise App
$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig(),
	'templates.path' => './templates'
));

// Initialise Twig view
$view = $app->view();
$view->parserOptions = array(
	'debug' => true,
	'cache' => dirname(__FILE__) . '/cache'
);

// Routes
$app->get('/', function() use ($app, $view) {
	$app->render('index.html', array('testvar' => 'sdfsd'));
});

// Run App
$app->run();
