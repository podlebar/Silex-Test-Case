<?php
namespace ExampleApp\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\Validator\Constraints;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;

class LoginControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
    	$controllers = new ControllerCollection();
    	
		$controllers->match('/', function() use ($app) {
			 return $app['twig']->render('login/login.twig');
		})->bind('login');
		
        return $controllers;
    }
}