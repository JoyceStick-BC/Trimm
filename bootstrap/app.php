<?php

use Respect\Validation\Validator as v;

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true,
		'determineRouteBeforeAppMiddleware' => true,
	    'addContentLengthHeader' => false,
		'db' => [
			'driver' => 'mysql',
			'host' => getenv('DB_HOST'),
			'database' => getenv('DB_NAME'),
			'username' => getenv('DB_USER'),
			'password' => getenv('DB_PASS'),
			'collation' => 'latin1_swedish_ci',
			'prefix' => ''
		]
	]
]);

$container = $app->getContainer();

//set up eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

//Add Db to container
$container['db'] = function ($container) use ($capsule) {
	return $capsule;
};

$container['auth'] = function($container) {
	return new \Carbon\Auth\Auth;
};

$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages;
};

$container['view'] = function ($container) {
	$view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
		'cache' => false
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$view->getEnvironment()->addGlobal('auth', [
		'check' => $container->auth->check(),
		'user' => $container->auth->user()
	]);

	$view->getEnvironment()->addGlobal('flash', $container->flash);

	return $view;
};

$container['validator'] = function($container) {
	return new \Carbon\Validation\Validator;
};

$container['HomeController'] = function($container) {
	return new \Carbon\Controllers\HomeController($container);
};

$container['AuthController'] = function($container) {
	return new \Carbon\Controllers\Auth\AuthController($container);
};

$container['PasswordController'] = function ($container) {
    return new \Carbon\Controllers\Auth\PasswordController($container);
};

$container['BundleController'] = function ($container) {
    return new \Carbon\Controllers\BundleController($container);
};

$container['DashboardController'] = function ($container) {
    return new \Carbon\Controllers\DashboardController($container);
};

$container['csrf'] = function($container) {
	return new \Slim\Csrf\Guard;
};

$container['es'] = function($container) {

	 /*$bundles = Bundle::all();

        $bundles->each(function($bundle) use ($client) {
            //create an index to be searched for every bundle
            $params = [
                'index' => 'bundles',
                'type' => 'bundle',
                'id' => $bundle->id,
                'body' => [
                    'user' => $bundle->user,
                    'bundleName' => $bundle->bundleName
                ]
            ];

            $indexed = $client->index($params);
    });*/

    /*
    * https://www.elastic.co/guide/en/elasticsearch/client/php-api/5.0/_indexing_documents.html
    */

	$client = new Elasticsearch\ClientBuilder;
	return $client->create()->build();
};

//middle ware
$app->add(new \Carbon\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \Carbon\Middleware\OldInputMiddleware($container));
$app->add(new \Carbon\Middleware\CsrfViewMiddleware($container));
$app->add($container->csrf);

v::with('Carbon\\Validation\\Rules');

require __DIR__ . '/../app/routes.php';