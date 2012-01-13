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
            $constraint = new Constraints\Collection(array(
                'username' => array(
                    new Constraints\NotBlank(),
                ),
                'password' => array(
                    new Constraints\NotBlank(),
                ),
            ));

            $data = array(); 
            $builder = $app['form.factory']->createBuilder('form', $data, array('validation_constraint' => $constraint));

            $form = $builder
                ->add('username', 'text', array('label' => 'Username:'))
                ->add('password', 'password', array('label' => 'Password:'))
                ->getForm();
                
            if ('POST' === $app['request']->getMethod()) {
                $form->bindRequest($app['request']);

                $data = $form->getData();

                $sql = "SELECT user_id, username FROM user WHERE username = ? AND password = ? AND validated = 1";
                $login = $app['db']->fetchAssoc($sql, array($data['username'], md5($data['password'])));
                if(!$login) $form->addError(new FormError("Whoopssyy.. wrong credentials. Or maybe your account isn't validated"));

                if ($form->isValid()) {
                    $app['session']->set('user', array('username' => $login['username'], 'user_id' => $login['user_id']));
                    $app['session']->setFlash('success', 'Welcome ' . $login['username']);

                    return $app->redirect($app['url_generator']->generate('homepage'));
                }
            }
            
            return $app['twig']->render('login/login.twig', array(
                'form' => $form->createView(),
            ));
        })->bind('login');
        
        $controllers->get('/logout', function() use ($app) {
            $app['session']->remove('user');
            $app['session']->setFlash('warning', 'You are not logged in anymore');
            return $app->redirect($app['url_generator']->generate('homepage'));
        })->bind('logout');
        return $controllers;
    }
}