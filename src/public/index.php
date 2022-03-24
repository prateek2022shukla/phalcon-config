<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";

use LDAP\Connection;
use LDAP\Result;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Http\Response;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Di;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;




$config = new Config([]);
use Phalcon\Mvc\Model\Behavior\Timestampable;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);


$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);



$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => $config->get("app")->get("dbname"),
                ]
        );
    }
);


$container->set(
    'config',
    function () {
        $fileName = '../app/etc/config.php';
        $factory = new ConfigFactory();
        return $factory->newInstance("php", $fileName);

        // $config = new Config([]);
        // $array = new \Phalcon\Config\Adapter\Php($fileName);
        // $config->merge($array);
        // return $config;
    },
    true
);


$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);



try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    $response = new Response();
    $response->setStatusCode(404, 'Not Found');
    $response->setContent("<h1>Sorry, the page doesn't exist<h1>");
    $response->send();
}
