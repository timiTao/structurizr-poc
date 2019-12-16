<?php

use StructurizrPHP\Core\Workspace;

require __DIR__ . '/../../vendor/autoload.php';

$workspace = new Workspace((string)\getenv('STRUCTURIZR_WORKSPACE_ID'), 'Simple', 'Example model with test ');
$model = $workspace->getModel();