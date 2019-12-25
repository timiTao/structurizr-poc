<?php

declare(strict_types=1);

use StructurizrPHP\Core\Model\Location;
use StructurizrPHP\Core\Model\Relationship\InteractionStyle;
use StructurizrPHP\Core\Model\Tags;
use StructurizrPHP\Core\View\Configuration\Shape;

require __DIR__ . '/general/workspace.php';

$user = $model->addPerson('User', '', Location::external());
$system = $model->addSoftwareSystem('Another system', '', Location::external());
$system->addTags('External '. Tags::SOFTWARE_SYSTEM);

$application = $model->addSoftwareSystem('Application', 'General application', Location::internal());
$application->addTags('Internal '. Tags::SOFTWARE_SYSTEM);
$integration = $model->addSoftwareSystem('Integration', 'General integration with 3th party', Location::external());
$integration->addTags('External '. Tags::SOFTWARE_SYSTEM);

$user->uses($application, 'uses');
$system->usesSoftwareSystem($application, 'uses');
$application->usesSoftwareSystem($integration);

$spiralContainer = $application->addContainer('Spiral', 'Spiral PHP-GRPC', 'GO');
$spiralLibrary = $spiralContainer->addComponent('Spiral/PHP-GRPC', 'Library', '');
$phpLibrary = $spiralContainer->addComponent('PHP/Worker', 'Library', '');
$spiralLibrary->usesComponent($phpLibrary, 'Uses', 'Socket', InteractionStyle::synchronous());
$user->usesComponent($spiralLibrary, 'Uses', 'gRPC',InteractionStyle::synchronous());

$coreContainerView = $workspace->getViews()->createContainerView($application, 'test','Core components');
$coreContainerView->addContainer($spiralContainer);
$coreContainerView->setAutomaticLayout(true);

$coreComponentView = $workspace->getViews()->createComponentView($spiralContainer, 'Spiral Component','Spiral Component');
$coreComponentView->addAllComponents();
$coreComponentView->addPerson($user);
$coreContainerView->setAutomaticLayout(true);

$styles = $workspace->getViews()->getConfiguration()->getStyles();

$styles->addElementStyle('Internal '. Tags::SOFTWARE_SYSTEM)->background("#1168bd")->color('#ffffff')->shape(Shape::folder());
$styles->addElementStyle(Tags::PERSON)->background("#08427b")->color('#ffffff')->shape(Shape::person());

$applicationSystemView = $workspace->getViews()->createSystemContextView($application, 'General use', 'General architecture');
$applicationSystemView->addAllElements();
$applicationSystemView->setAutomaticLayout(true);

include_once 'general/adr.php';
include_once 'general/export.php';


