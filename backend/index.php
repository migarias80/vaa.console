<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \utils\LogUtils;
use \responses\ErrorResponse;

// Autoload
require './vendor/autoload.php';
spl_autoload_register(function ($classname) {
    require_once ($classname . ".php");
});

// Propiedades VAA web
require '../config/Properties.php';

// Configuracion de API REST
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader' => false
    ],
];

// PHP 7
/*$c = new \Slim\Container($configuration);
$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $metodo = $request->getMethod();
        $uri = $request->getUri();
        $basePath = $uri->getBasePath();
        $path = $uri->getPath();

        LogUtils::ERROR($exception->getMessage(), $metodo . " " . $basePath . $path, "Exception");
        return (new ErrorResponse("Ocurrio un error inesperado al realizar la accion", CODE_ERROR_INESPERADO, $exception))->GetResponse();
    };
};
$app = new \Slim\App($c);
*/

// PHP 8
error_reporting(E_ALL);
ini_set('display_errors', '1');
use DI\Container;
use Slim\Factory\AppFactory;
$c = new Container($configuration);
$c->set('errorHandler', function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $metodo = $request->getMethod();
        $uri = $request->getUri();
        $basePath = $uri->getBasePath();
        $path = $uri->getPath();

        LogUtils::ERROR($exception->getMessage(), $metodo . " " . $basePath . $path, "Exception");
        return (new ErrorResponse("Ocurrio un error inesperado al realizar la accion", CODE_ERROR_INESPERADO, $exception))->GetResponse();
    };
});
$app = AppFactory::createFromContainer($c);
$app->setBasePath('/vaa/backend');
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Rutas REST
$routeFiles = (array) glob('controller/*.php');
foreach($routeFiles as $routeFile) {
    require $routeFile;
}

$app->run();