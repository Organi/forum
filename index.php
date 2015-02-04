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
	$app->render('index.html');
});

// Save Forum
$app->post('/forum/save', function() use ($app, $view) {

	$forum = new \Forum();
	$forum->name = $app->request->post('name');

	$uri = md5(uniqid());
	while (\Forum::where('url', '=', $uri)->exists()) {
		$uri = md5(uniqid());
	}

	$forum->url = $uri;
	$forum->save();

	$post = new \Post();
	$post->forum_id = $forum->id;
	$post->user = $app->request->post('user');
	$post->content = $app->request->post('content');
	$post->save();

	$app->redirect("/forum/{$forum->url}");
});

// Save Post
$app->post('/forum/post/save', function() use ($app, $view) {

	// validate forum_id TODO

	$post = new \Post();
	$post->forum_id = $app->request->post('forum_id');
	$post->user = $app->request->post('user');
	$post->content = $app->request->post('content');
	$post->save();

	$url = \Forum::where('id', '=', $post->forum_id)->pluck('url');

	$app->redirect("/forum/{$url}");
});

// View Forum
$app->get('/forum/:uri', function($uri) use ($app, $view) {
	$forum = \Forum::where('url', '=', $uri)->first();
	$posts = \Post::where('forum_id', '=', $forum->id)->get();

	$app->render('forum.html', 
		array(
			'forum' => $forum, 
			'posts' => $posts
		)
	);
});

// Catch All
$app->map('/:all', function($all) use ($app) {
	$app->redirect('/');
})->conditions(array('all' => '.+'))->via('GET', 'POST');

// Run App
$app->run();
