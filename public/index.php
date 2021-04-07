<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AppController;
use App\Controllers\NotFoundController;
use App\Services\FinnhubStockExchangeService;
use App\Services\GetQuoteService;
use App\Services\StockExchangeService;
use League\Container\Container;


session_start();

$container = new Container();
$container->add(StockExchangeService::class, FinnhubStockExchangeService::class);
$container->add(GetQuoteService::class)->addArgument(StockExchangeService::class);
$container->add(AppController::class)->addArgument(GetQuoteService::class);



$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->get('/', [AppController::class, 'showMainPage']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo (new NotFoundController())->index();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo (new NotFoundController())->index();
        break;
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $container->get($class)->$method($vars);
}