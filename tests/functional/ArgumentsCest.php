<?php

class ArgumentsCest
{
    /** @var string */
    private $outputFolder;

    /** @var string */
    private $configFolder;

    public function _before()
    {
        @mkdir(codecept_output_dir() . 'arg');
        $this->outputFolder = codecept_output_dir('arg');
        $this->configFolder = codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'config');
    }

    public function _after()
    {
        array_map('unlink', glob($this->outputFolder . DIRECTORY_SEPARATOR . '*.json'));
        rmdir(codecept_output_dir() . 'arg');
    }

    /**
     * @param FunctionalTester $tester
     */
    public function testInvokeArguments(FunctionalTester $tester)
    {
        $source = codecept_data_dir('Project' . DIRECTORY_SEPARATOR . 'PackageA' . DIRECTORY_SEPARATOR . 'Controller');
        $target = $this->outputFolder . DIRECTORY_SEPARATOR . 'inheritance.json';

        $cmd = $tester->getTool() . ' -q analyze ' . $this->configFolder .  DIRECTORY_SEPARATOR . 'class.yml '
               . '-m inheritance '
               . '-s ' . $source . ' '
               . '-p *.php '
               . '-f "PhpDA\Writer\Strategy\Json" '
               . '-t ' . $target;

        exec($cmd, $output, $return);

        $expected = [
            "PackageA\\Controller\\AbstractController=>Zend\\Mvc\\Controller\\AbstractController",
            "PackageA\\Controller\\Command=>PackageA\\Controller\\AbstractController",
            "PackageA\\Controller\\Query=>PackageA\\Controller\\AbstractController",
        ];

        $result = array_keys(json_decode(file_get_contents($target), true)['edges']);

        $tester->assertSame($expected, $result);
        $tester->assertSame(0, $return);
    }
}
