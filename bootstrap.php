<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/vendor/autoload.php';


/*spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    try {
        include __DIR__ . "/src/$class.php";
        return;
    }
    catch (Throwable $exception) {
        // Do nothing ?
    }
});*/

set_error_handler(function() { /* ignore errors */ });