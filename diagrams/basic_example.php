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
use StructurizrPHP\Core\Workspace;
use Symfony\Component\HttpClient\Psr18Client;

require __DIR__ . '/../vendor/autoload.php';

$workspace = new Workspace((string)\getenv('STRUCTURIZR_WORKSPACE_ID'), 'Simple', 'Example model with test ');
$model = $workspace->getModel();

$user = $model->addPerson('User', '', Location::external());
$system = $model->addSoftwareSystem('Another system', '', Location::external());

$documentation = $model->addSoftwareSystem('Documentation', '', Location::internal());

$application = $model->addSoftwareSystem('Application', 'General application', Location::internal());
$integration = $model->addSoftwareSystem('Integration', 'General integration with 3th party', Location::external());
$user->uses($application, 'uses');
$system->usesSoftwareSystem($application, 'uses');
$application->usesSoftwareSystem($integration);

$applicationSystemView = $workspace->getViews()->createSystemContextView($application, 'General use', 'General architecture');
$applicationSystemView->addAllElements();
$applicationSystemView->setAutomaticLayout(true);

//region Documentation of ADR
$adrDirectory = __DIR__ . '/../doc/adr';
$adrToolsImporter = new AdrToolsImporter($workspace, $adrDirectory);
$adrToolsImporter->importArchitectureDecisionRecords($documentation);
//endregion

//region Export workspace
$client = new Client(
    new Credentials((string)\getenv('STRUCTURIZR_API_KEY'), (string)\getenv('STRUCTURIZR_API_SECRET')),
    new UrlMap('https://api.structurizr.com'),
    new Psr18Client(),
    new SymfonyRequestFactory(),
    (new Logger('structurizr'))->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/' . basename(__FILE__) . '.log', Logger::DEBUG))
);
$client->put($workspace);
//endregion
