<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use StructurizrPHP\AdrTools\AdrToolsImporter;
use StructurizrPHP\Client\Client;
use StructurizrPHP\Client\Credentials;
use StructurizrPHP\Client\Infrastructure\Http\SymfonyRequestFactory;
use StructurizrPHP\Client\UrlMap;
use StructurizrPHP\Core\Model\Location;
use StructurizrPHP\Core\Model\Relationship\InteractionStyle;
use StructurizrPHP\Core\Workspace;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

$workspace = new Workspace((string)\getenv('STRUCTURIZR_WORKSPACE_ID'), 'Simple', 'Example model with test ');
$model = $workspace->getModel();

$documentation = $model->addSoftwareSystem('Documentation', '', Location::unspecified());
$moduleB = $model->addSoftwareSystem('Module B', '', Location::internal());
$moduleA = $model->addSoftwareSystem('Module A', '', Location::internal());
$architecture = $model->addSoftwareSystem('Architecture', 'General view', Location::external());

$architecture->usesSoftwareSystem($moduleA, 'Uses', null, InteractionStyle::asynchronous())
$architecture->usesSoftwareSystem($moduleB, 'Uses', null, InteractionStyle::synchronous());

$moduleA->usesSoftwareSystem($moduleB);

$generalArchitectureView = $workspace->getViews()->createSystemContextView($architecture, 'architecture', 'General architecture');
$generalArchitectureView->addAllElements();
$generalArchitectureView->setAutomaticLayout(true);

//region Documentation of ADR
$adrDirectory = __DIR__ . '/../doc/adr';
$adrToolsImporter = new AdrToolsImporter($workspace, $adrDirectory);
$adrToolsImporter->importArchitectureDecisionRecords($documentation);
//endregion

$client = new Client(
    new Credentials((string)\getenv('STRUCTURIZR_API_KEY'), (string)\getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/' . basename(__FILE__) . '.log', Logger::DEBUG))
);
$client->put($workspace);
