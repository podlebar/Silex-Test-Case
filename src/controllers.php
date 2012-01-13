<?php
$app->match('/', function() use ($app) {

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
