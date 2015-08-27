<?php
/**
 * User: Andy Abbott
 * Date: 26/08/2015
 * Time: 10:29
 */

function __autoload($class_name) {
    $path = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$class_name . '.php';
    $path = str_replace('\\',DIRECTORY_SEPARATOR,$path);
    $path = str_replace('/',DIRECTORY_SEPARATOR,$path);
    $path = realpath($path);
    include_once $path;

    //ToDo: Add exception handling
}
