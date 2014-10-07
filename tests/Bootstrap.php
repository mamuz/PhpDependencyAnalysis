<?php

error_reporting(E_ALL);

$file = __DIR__ . '/../vendor/autoload.php';
if (file_exists($file)) {
    $loader = require $file;
}

if (!isset($loader)) {
    throw new \RuntimeException('Cannot find vendor/autoload.php');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader->add('PhpDATest\\', __DIR__);

unset($file, $loader);
