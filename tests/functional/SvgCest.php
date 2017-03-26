<?php

class SvgCest
{
    /** @var string */
    private $expectationFolder;

    /** @var string */
    private $outputFolder;

    /** @var \DirectoryIterator */
    private $dir;

    /** @var array */
    private $resultsWithViolations = ['total', 'packages', 'layers', 'complex-cycle', 'namespace-filter'];

    public function _before()
    {
        @mkdir(codecept_output_dir() . 'svg');
        $this->expectationFolder = codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'expectation');
        $this->outputFolder = codecept_output_dir('svg');
        $this->dir = new \DirectoryIterator(codecept_data_dir('svg' . DIRECTORY_SEPARATOR . 'config'));
    }

    public function _after()
    {
        array_map('unlink', glob($this->outputFolder . DIRECTORY_SEPARATOR . '*.svg'));
        rmdir(codecept_output_dir() . 'svg');
    }

    /**
     * @param FunctionalTester $tester
     */
    public function testGraphCreationForAllUseCases(FunctionalTester $tester)
    {
        foreach ($this->dir as $file) {
            if (!$file->isDot()) {

                exec($tester->getTool() . ' -q analyze ' . $file->getRealPath(), $output, $return);

                $svgFilename = DIRECTORY_SEPARATOR . $file->getBasename('yml') . 'svg';
                $expected = $tester->readGraphNodeTitlesFrom($this->expectationFolder . $svgFilename);
                $result = $tester->readGraphNodeTitlesFrom($this->outputFolder . $svgFilename);

                $tester->assertNotEmpty($expected, $file->getBasename());
                $tester->assertNotEmpty($result, $file->getBasename());
                $tester->assertEquals($expected, $result, $file->getBasename());

                if (in_array($file->getBasename('.yml'), $this->resultsWithViolations)) {
                    $tester->assertSame(1, $return);
                } else {
                    $tester->assertSame(0, $return);
                }
            }
        }
    }
}
