<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Parser;

$app->match('/', function() use ($app) {
	/*$yaml = new Parser();
	$value = $yaml->parse(file_get_contents('/path/to/file.yaml'));
	var_dump($value);die;*/
	//return 'foo';
	return $app['twig']->render('welcome.twig', array(
        'pagetitle' => 'Welcome',
    ));
})->bind('homepage');


// ************************************************
// Mount Controllers
// ************************************************

$app->mount('/register', new \ExampleApp\Provider\Controller\RegisterControllerProvider());
$app->mount('/login', new \ExampleApp\Provider\Controller\LoginControllerProvider());

return $app;
