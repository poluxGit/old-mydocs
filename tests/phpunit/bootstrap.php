<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author polux
 */

// TODO: check include path
ini_set(
        'include_path',
        ini_get('include_path').
        PATH_SEPARATOR.dirname(__FILE__).
        '/usr/share/pear'.PATH_SEPARATOR.dirname(__FILE__).
        '/opt/php-unit/sources/phpunit/Framework'
);

require './../../vendor/autoload.php';
