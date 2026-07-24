<?php

spl_autoload_register(function($classname){
    $directories = ['models', 'core', 'controllers', 'middleware'];
    foreach ($directories as $dir) {
        $file = "../app/{$dir}/" . ucfirst($classname) . ".php";
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

require '../vendor/autoload.php';

require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'Middleware.php';
require 'Router.php';
