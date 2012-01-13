<?php
namespace ExampleApp\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\Validator\Constraints;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;

class RegisterControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection();

        $controllers->match('/', function () use ($app) {

            $constraint = new Constraints\Collection(array(
                'username' => array(
                    new Constraints\NotBlank(),
                    new Constraints\MinLength(5),
                ),
                'email' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Email(),
                ),
                'password' => array(
                    new Constraints\NotBlank(),
                    new Constraints\MinLength(7),
                ),
                'birthdate' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Date(),
                ),
                'language' => array(
                    new Constraints\NotBlank(),
                ),
                'file' => array(
                    new Constraints\NotNull(),
                ),
            ));

            $data = array(); 
            $builder = $app['form.factory']->createBuilder('form', $data, array('validation_constraint' => $constraint));

            $form = $builder
                ->add('username', 'text', array('label' => 'Username:'))
                ->add('email', 'email', array('label' => 'Email:'))
                ->add('password', 'password', array('label' => 'Password'))
                ->add('birthdate', 'date', array(
                    'input'  => 'string',
                    'widget' => 'choice',
                    'years'	 => range(1960, date('Y')-10),
                ))
                ->add('language', 'choice', array(
                    'choices' => array('de' => 'German', 'en' => 'English'),
                    'preferred_choices' => array('de'),
                    'multiple'  => false,
                    'expanded'  => true,
                    'label'		=> 'Language:',
                ))
                ->add('file', 'file')
                ->getForm();

            if ('POST' === $app['request']->getMethod()) {
                $form->bindRequest($app['request']);

                $data = $form->getData();
                // Check if username is available
                $sql = "SELECT user_id FROM user WHERE username = ?";
                $post = $app['db']->fetchAssoc($sql, array($data['username']));	
                if($post) $form->addError(new FormError('Whoopssyy.. this username is already taken.'));

                $sql = "SELECT user_id FROM user WHERE email = ?";
                $post = $app['db']->fetchAssoc($sql, array($data['email']));	
                if($post) $form->addError(new FormError('There is already a user with this email.'));

                if ($form->isValid()) {
                    $data['password'] = md5($data['password']);
                    $data['userhash'] = uniqid();
                    unset($data['file']); //delete the file item because is not stored in db

                    $app['db']->insert('user', $data);
                    $id = $app['db']->lastInsertId();

                    $files = $app['request']->files->get($form->getName());

                    $path = $app['app_config']['uploads']['path'];                
                    $filename = $files['file']->getClientOriginalName();
                    $files['file']->move($path,$filename);

                    $emailmessage = 'your validation hash: '. $data['userhash'];
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Email for ya')
                        ->setFrom(array($app['app_config']['email']['sendfrom']))
                        ->setTo(array($data['email']))
                        ->setBody($emailmessage);

                    $app['mailer']->send($message);

                    $app['monolog']->addInfo('New User: ' . $data['email'] . ' ID: ' .$id);
                    $app['session']->setFlash('success', 'Hey.. you just registered your account. Please check your email for the validation code');

                    return $app->redirect($app['url_generator']->generate('validateregistration'));
                }
            }

            return $app['twig']->render('register/register.twig', array(
                'form' => $form->createView(),
            ));
        })->bind('register');


        $controllers->match('/validate', function() use ($app) {

            $constraint = new Constraints\Collection(array(
                'code' => array(
                    new Constraints\NotBlank(),
                ),
            ));

            $builder = $app['form.factory']->createBuilder('form', null, array('validation_constraint' => $constraint));

            $form = $builder
                ->add('code', 'text', array('label' => 'Validation-Code:'))
                ->getForm();

            if ('POST' === $app['request']->getMethod()) {
                $form->bindRequest($app['request']);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $sql = "SELECT username ,userhash FROM user WHERE userhash = ? AND validated = 0";
                    $post = $app['db']->fetchAssoc($sql, array($data['code']));	
                    if(!$post) {
                        $app['session']->setFlash('error', 'could not validate');
                    } else {
                        $app['session']->setFlash('success', 'validate user' . $post['username']);
                        $app['db']->update('user', array('validated' => '1'), array('userhash' => $data['code']));
                    }

                    return $app->redirect($app['url_generator']->generate('validateregistration'));
                }
            }

            return $app['twig']->render('register/validate.twig', array(
                'form' => $form->createView(),
            ));
        })->bind('validateregistration');

        $controllers->get('/validate/{code}', function($code) use ($app) {
            $sql = "SELECT username ,userhash FROM user WHERE userhash = ? AND validated = 0";
            $post = $app['db']->fetchAssoc($sql, array($code));	
            if(!$post) {
                $app['session']->setFlash('error', 'could not validate');
            } else {
                $app['session']->setFlash('success', 'validate user' . $post['username']);
                $app['db']->update('user', array('validated' => '1'), array('userhash' => $code));
            }

            return $app->redirect($app['url_generator']->generate('validateregistration'));
        });

        return $controllers;
    }
}
