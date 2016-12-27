<?php

use MyGED\Application\Application;

/**
 * PHP Unit tests bootstrap file
 */
require __DIR__ . './../vendor/autoload.php';

// Force initialization of Application (files and Databases)!
MyGED\Application\Application::initApplication(null, true);
