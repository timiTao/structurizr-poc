<?php

declare(strict_types=1);

include_once 'workspace.php';

use StructurizrPHP\AdrTools\AdrToolsImporter;

//region Documentation of ADR
$adrDirectory = __DIR__ . '/../../doc/adr';
$adrToolsImporter = new AdrToolsImporter($workspace, $adrDirectory);
$adrToolsImporter->importArchitectureDecisionRecords($application);
//endregion