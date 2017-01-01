<?php


use MyGED\Application\Application;

/**
 * PHP Unit tests bootstrap file
 */
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../sources/application/application.class.php';
require __DIR__ . '/../sources/core/filesystem/filesystem.sclass.php';
require __DIR__ . '/../sources/core/database/database.sclass.php';
require __DIR__ . '/../sources/core/database/dbobject.aclass.php';
require __DIR__ . '/../sources/vault/vault.sclass.php';

require __DIR__ . '/../sources/vault/vaultdb.sclass.php';
require __DIR__ . '/../sources/vault/vaultfs.sclass.php';



// Force initialization of Application (files and Databases)!
Application::initApplication(null, true);
