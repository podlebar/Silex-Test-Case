<?php
use Symfony\Component\Yaml\Parser;

$app = new Silex\Application();
$app['debug'] = true;

$app['autoloader']->registerNamespaces(array(
    'Symfony'   => __DIR__.'/../vendor',
    'ExampleApp'  => __DIR__.'/',
));
$app['autoloader']->registerPrefix('Twig_', __DIR__.'/../vendor/silex/vendor/twig/lib');

$yaml = new Parser();
$app['provider_config'] = $yaml->parse(file_get_contents(__DIR__.'/../config/provider.yaml'));
$app['app_config'] = $yaml->parse(file_get_contents(__DIR__.'/../config/app.yaml'));

// ************************************************
// Register built in Providers
// ************************************************


// Sessions
$app->register(new Silex\Provider\SessionServiceProvider());

// Symfony Bridges
$app->register(new Silex\Provider\SymfonyBridgesServiceProvider(), array(
    'symfony_bridges.class_path'  => __DIR__.'/vendor/symfony/src',
));
// Validation
$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.class_path'    => __DIR__.'/../vendor/Symfony/Component/Validator',
));

// Translations
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback'           => 'de',
    'translation.class_path'    => __DIR__.'/../vendor/symfony/src',
    'translator.messages' => array(),
));

// Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__.'/../views',
    'twig.class_path' => __DIR__.'/../vendor/silex/vendor/twig/lib',
    'debug' => true,
));

// Url Generator
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Forms
$app->register(new Silex\Provider\FormServiceProvider());

// Doctrine
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options'    => array(
        'driver'    => $app['provider_config']['doctrine']['driver'],
        'dbname'    => $app['provider_config']['doctrine']['db'],
        'host'      => $app['provider_config']['doctrine']['host'],
        'user'      => $app['provider_config']['doctrine']['user'],
        'password'  => $app['provider_config']['doctrine']['password'],
    ),
    'db.dbal.class_path'    => __DIR__ . '/../vendor/silex/vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__ . '/../vendor/silex/vendor/doctrine-common/lib',
));

// Monolog
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile'       => __DIR__.'/../log/development.log',
    'monolog.class_path'    => __DIR__.'/../vendor/silex/vendor/monolog/src',
    'monolog.level'			=> 'WARNING'
));

// Swiftmailer
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.options' => array(
        'host'       => $app['provider_config']['swiftmailer']['host'],
        'port'       => $app['provider_config']['swiftmailer']['port'],
        'username'   => $app['provider_config']['swiftmailer']['username'],
        'password'   => $app['provider_config']['swiftmailer']['password'],
        'encryption' => $app['provider_config']['swiftmailer']['encryption'],
        'auth_mode'  => $app['provider_config']['swiftmailer']['auth_mode']),
    'swiftmailer.class_path' => __DIR__.'/../vendor/swiftmailer/lib/classes'
));


// ************************************************
// Filters
// ************************************************


$app->before(function () use ($app) {
    $app['twig']->addGlobal('layout', $app['twig']->loadTemplate('layout/layout.twig'));
});

return $app;
