<?php

declare(strict_types=1);

include_once 'workspace.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use StructurizrPHP\Client\Client;
use StructurizrPHP\Client\Credentials;
use StructurizrPHP\Client\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\Client\UrlMap;
use Symfony\Component\HttpClient\Psr18Client;

//region Export workspace
$client = new Client(
    new Credentials((string)\getenv('STRUCTURIZR_API_KEY'), (string)\getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/../../var/logs/' . basename(__FILE__) . '.log', Logger::DEBUG))
);
$client->put($workspace);
//endregion