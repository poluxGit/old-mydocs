<?php


/**
 * Index Page - Main Web Start Script
 *
 * @author polux <polux@poluxfr.org>
 */
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../sources/include-all.php';

use MyGED\Vault as Vault;
use MyGED\Application\Application;
use MyGED\Business\Document;
use MyGED\Process\Engines\ImportFiles;

// Application init!
Application::initApplication();

$lStrDirToImport =  __DIR__.'/../tests/tests-ressources/';

echo sprintf("IMPORTING ALL DOCUMENTS FROM '%s' <BR/>", $lStrDirToImport);

/** @var Document $lODoc */
$lStrImportFilesTaksUID = MyGED\Process\Engines\ImportFiles::createNewImportTask();

print_r($lStrImportFilesTaksUID);

$lObjImportFiles = new ImportFiles(null);
$lObjImportFiles->loadDB($lStrImportFilesTaksUID);
$lObjImportFiles->setInputDirectoryPath($lStrDirToImport);


echo "Result with No recursive call : <BR/>";
print_r(count($lObjImportFiles->launchImportDirectory(false)));

echo "Result with recursive call : <BR/>";
print_r(count($lObjImportFiles->launchImportDirectory(true)));
exit;
