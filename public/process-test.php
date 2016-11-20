<?php

/**
 * Script de test des events et Process
 *
 * Script Description
 *
 * @package Process
 * @subpackage Process
 * @author polux@poluxfr.org
 *
 */

// Ouverture session HTTP pour stockage des process en cours
//session_start();

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../sources/core/filesystem/pdfhandler.class.php';
require __DIR__.'/../sources/core/application.class.php';

use MyGED\Core\FileSystem\PDFHandler as PDFHandler;
use MyGED\Core\Application as Application;

Application::initApplication();

$lObjPDF = new PDFHandler('./test.pdf');
echo "Pages Count : ".strval($lObjPDF->getPagesCount());
$lArrMeta = $lObjPDF->getAllMetaValues();

echo "<br>"."Meta Data";

echo "<pre>";
print_r($lArrMeta);
echo "</pre>";

echo "Files splitted";
$lArrFiles = PDFHandler::splitPDFPageByPage('./test.pdf');

echo "<pre>";
print_r($lArrFiles);
echo "</pre>";

echo "OCR Content";
$lArrOCR = PDFHandler::launchOCRAnalysis('./test.pdf');

echo "<pre>";
print_r($lArrOCR);
echo "</pre>";

exit;
