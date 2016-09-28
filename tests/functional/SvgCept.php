<?php

/** @var \Codeception\Scenario $scenario $I */
$I = new FunctionalTester($scenario);
$I->wantTo('perform analysis and see svg results');

@mkdir(codecept_output_dir() . 'svg');

$configFolder = codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'config');
$expectationFolder = codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'expectation');
$outputFolder = codecept_output_dir('svg');

array_map('unlink', glob($outputFolder . DIRECTORY_SEPARATOR . '*.svg'));

$dir = new \DirectoryIterator($configFolder);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot() && $fileinfo->getBasename() !== 'packages.yml') {
        exec('./bin/phpda analyze ' . $fileinfo->getRealPath(), $output);
        $resultFile = $outputFolder . DIRECTORY_SEPARATOR . $fileinfo->getBasename('yml') . 'svg';
        $expectationFile = $expectationFolder . DIRECTORY_SEPARATOR . $fileinfo->getBasename('yml') . 'svg';
        if (md5($expectationFile) !== md5($resultFile)) {
            throw new \Exception($fileinfo->getBasename() . ' not working');
        }
    }
}
