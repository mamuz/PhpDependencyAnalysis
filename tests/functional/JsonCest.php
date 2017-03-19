<?php

class JsonCest
{
    /** @var string */
    private $expectationFolder;

    /** @var string */
    private $outputFolder;

    /** @var \DirectoryIterator */
    private $dir;

    /** @var array */
    private $resultsWithViolations = ['total', 'packages', 'layers', 'complex-cycle'];

    public function _before()
    {
        @mkdir(codecept_output_dir() . 'json');
        $this->expectationFolder = codecept_data_dir('json' . DIRECTORY_SEPARATOR . 'expectation');
        $this->outputFolder = codecept_output_dir('json');
        $this->dir = new \DirectoryIterator(codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'config'));
    }

    public function _after()
    {
        array_map('unlink', glob($this->outputFolder . DIRECTORY_SEPARATOR . '*.json'));
        rmdir(codecept_output_dir() . 'json');
    }

    /**
     * @param FunctionalTester $tester
     */
    public function testResultCreationForAllUseCases(FunctionalTester $tester)
    {
        // @todo using namespacefilter
        foreach ($this->dir as $file) {
            if (!$file->isDot()) {

                $target = './tests/_output/json/'  . $file->getBasename('yml') . 'json';

                $cmd = './bin/phpda -q analyze ' . $file->getRealPath()
                       . ' -f "PhpDA\Writer\Strategy\Json"'
                       . ' -t ' . $target;

                exec($cmd, $output, $return);

                $jsonFilename = DIRECTORY_SEPARATOR . $file->getBasename('yml') . 'json';
                $expected = json_decode(file_get_contents($this->expectationFolder . $jsonFilename), true);
                $result = json_decode(file_get_contents($this->outputFolder . $jsonFilename), true);

                $tester->assertNotEmpty($expected, $file->getBasename());
                $tester->assertNotEmpty($result, $file->getBasename());
                $tester->assertSame($expected, $result, $file->getBasename());

                if (in_array($file->getBasename('.yml'), $this->resultsWithViolations)) {
                    $tester->assertSame(1, $return);
                } else {
                    $tester->assertSame(0, $return);
                }
            }
        }
    }
}
