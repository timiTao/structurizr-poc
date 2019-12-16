<?php

declare(strict_types=1);

use StructurizrPHP\Core\Model\Location;

require __DIR__ . '/general/workspace.php';

$user = $model->addPerson('User', '', Location::external());
$system = $model->addSoftwareSystem('Another system', '', Location::external());

$application = $model->addSoftwareSystem('Application', 'General application', Location::internal());
$integration = $model->addSoftwareSystem('Integration', 'General integration with 3th party', Location::external());
$user->uses($application, 'uses');
$system->usesSoftwareSystem($application, 'uses');
$application->usesSoftwareSystem($integration);

$applicationSystemView = $workspace->getViews()->createSystemContextView($application, 'General use', 'General architecture');
$applicationSystemView->addAllElements();
$applicationSystemView->setAutomaticLayout(true);

include_once 'general/adr.php';
include_once 'general/export.php';


