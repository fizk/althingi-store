<?php
chdir(dirname(__DIR__));
include __DIR__ . '/../vendor/autoload.php';

use App\Response\ErrorResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\ServiceManager\ServiceManager;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\Diactoros\Response\EmptyResponse;

use function App\read_resource;
use function App\exception_error_handler;

set_error_handler('App\\exception_error_handler');
mb_parse_str(read_resource(fopen("php://input", "r")), $bodyQuery);

$request = ServerRequestFactory::fromGlobals(
        $_SERVER,
        $_GET,
        $bodyQuery,
        $_COOKIE,
        $_FILES
    );

$manager = new ServiceManager(require_once './config/service.php');
$emitter = new SapiEmitter();

$router = require_once('./config/routes.php');
$match = $router->match($request);

try {
    if ($match) {
        foreach ($match->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        $handler = $manager->get($match->getParam('handler'));
        $response = $handler->handle($request);
        $emitter->emit($response);
    } else {
        $emitter->emit(new EmptyResponse(404));
    }
} catch (\Throwable $e) {
    $emitter->emit(new ErrorResponse($e, $request));
}


