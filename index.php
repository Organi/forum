<?php
require_once 'vendor/autoload.php';

// Initialise App
$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig(),
	'templates.path' => './templates'
));

// Initialise Eloquent ORM
$settings = array(
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'forum',
	'username' => 'root',
	'password' => '',
	'charset'   => 'utf8',
	'collation' => 'utf8_general_ci',
	'prefix' => ''
);
$container = new \Illuminate\Container\Container();
$connFactory = new \Illuminate\Database\Connectors\ConnectionFactory($container);
$conn = $connFactory->make($settings);
$resolver = new \Illuminate\Database\ConnectionResolver();
$resolver->addConnection('default', $conn);
$resolver->setDefaultConnection('default');
\Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);

// Include Models
require_once 'models/forum.php';
require_once 'models/post.php';

// Initialise Twig view
$view = $app->view();
$view->parserOptions = array(
	'debug' => true,
	'cache' => dirname(__FILE__) . '/cache'
);

// Routes
$app->get('/', function() use ($app, $view) {

	$forums = \Forum::all();
	var_dump($forums);


	$app->render('index.html', array('testvar' => 'sdfsd'));
});

$app->get('/forum/:forumuri', function($uri) use ($app, $view) {
	// add conditional
	$forum = \Forum::all();
	$app->render('forum.html', array('forum' => $forum));
});

$app->post('/forum/save', function() use ($app, $view) {});
$app->post('/forum/post/save', function() use ($app, $view) {});











// Run App
$app->run();
