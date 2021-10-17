<?php
use Phalcon\Di;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\Model\Manager as ModelsManager;

use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Url as UrlResolver;

define('APP_PATH', dirname(__DIR__) . '/app');

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
		APP_PATH . "/repositories/",
		APP_PATH . "/repo/",
		APP_PATH . "/models/",
    ]
);


// Register namespaces
$loader->registerNamespaces(
    [
        'Linkfire\Assignment' => APP_PATH,
        'MyApp\Repos' => APP_PATH.'/repo',
        'PhalconRepositories' => APP_PATH.'/repositories',
        'MyApp\Models' => APP_PATH.'/models',
    ]
);

$loader->register();

$di = new Di();
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});



// Registering a router
$di->set('router', function(){
    $router = new \Phalcon\Mvc\Router();
    require __DIR__.'/../app/config/router.php';
    return $router;
});
// Registering a dispatcher
$di->set(
    'dispatcher',
    function () {
        $dispatcher = new Dispatcher();

        $dispatcher->setDefaultNamespace(
            'Linkfire\Assignment\Controllers'
        );

        return $dispatcher;
    }
);




$di->set(
    "modelsManager",
    function() {
        return new ModelsManager();
    }
);

$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

$di->set('serviceRepo', function () {
    return new MyApp\Repos\ServiceRepository(new \MyApp\Models\User());
});

// Registering a Http\Response
$di->set("response", Response::class);
// Registering a Http\Request
$di->set("request", Request::class);

// Registering the view component
$di->set(
    "view",
    function () {
        $view = new View();
        $view->setViewsDir("../apps/views/");
        return $view;
    }
);

try {
    include APP_PATH . '/config/services.php';

    $application = new \Phalcon\Mvc\Application($di);
    //echo $application->handle($_SERVER['REQUEST_URI'])->getContent();
    echo $application->handle($_GET['_url'] ?? '/')->getContent();

} catch (Exception $e) {
    echo $e->getMessage();
    //echo '<pre>' . $e->getTraceAsString() . '</pre>';

}