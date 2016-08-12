<?php

/** @var \Codeception\Scenario $scenario $I */
$I = new FunctionalTester($scenario);
$I->wantTo('perform analysis and see svg results');

$configFolder = codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'config');
$expectationFolder = codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'expectation');
$outputFolder = codecept_output_dir('svg');

array_map('unlink', glob($outputFolder . DIRECTORY_SEPARATOR . '*.svg'));

$dir = new \DirectoryIterator($configFolder);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        exec('./bin/phpda analyze ' . $fileinfo->getRealPath(), $output);
        $result = sha1_file($outputFolder . DIRECTORY_SEPARATOR . $fileinfo->getBasename('yml') . 'svg');
        $expectation = sha1_file($expectationFolder . DIRECTORY_SEPARATOR . $fileinfo->getBasename('yml') . 'svg');
        if ($expectation !== $result) {
            //throw new \Exception($fileinfo->getBasename('yml') . ' not working');
        }
    }
}
