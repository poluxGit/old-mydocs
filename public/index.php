<?php


/**
 * Index Page - Main Web Start Script
 *
 * @author polux <polux@poluxfr.org>
 */
require __DIR__.'/../vendor/autoload.php';

use MyGED\Vault as Vault;
use MyGED\Application\Application as Application;
use MyGED\Business\Document;

// Application init!
Application::initApplication();


echo "DISPLAYING ALL DOCUMENTS : <BR/>";

/** @var Document $lODoc */
$lODoc = new Document();

echo "<pre>";
print_r(Document::getAllClassItemsData());
echo "</pre>";


exit;
