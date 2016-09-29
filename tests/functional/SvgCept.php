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
    if (!$fileinfo->isDot()) {

        exec('./bin/phpda analyze ' . $fileinfo->getRealPath(), $output);

        $expectationFile = $expectationFolder . DIRECTORY_SEPARATOR . $fileinfo->getBasename('yml') . 'svg';
        $expectationXml = simplexml_load_file($expectationFile);
        $expectationXml->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
        $expected = array_map('strval', $expectationXml->xpath('/svg:svg/svg:g[1]/svg:g/svg:title'));
        sort($expected);

        $resultFile = $outputFolder . DIRECTORY_SEPARATOR . $fileinfo->getBasename('yml') . 'svg';
        $resultXml = simplexml_load_file($resultFile);
        $resultXml->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
        $result = array_map('strval', $resultXml->xpath('/svg:svg/svg:g[1]/svg:g/svg:title'));
        sort($result);

        if ($result !== $expected) {
            throw new \Exception($fileinfo->getBasename() . ' is not valid');
        }
    }
}
