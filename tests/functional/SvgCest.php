<?php

class SvgCest
{
    /** @var string */
    private $expectationFolder;

    /** @var string */
    private $outputFolder;

    /** @var \DirectoryIterator */
    private $dir;

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
    public function requestDefaultActionInDefaultController(FunctionalTester $tester)
    {
        $tester->wantTo('perform analysis and see svg results');

        foreach ($this->dir as $file) {
            if (!$file->isDot()) {

                exec('./bin/phpda -q analyze ' . $file->getRealPath(), $output);

                $svgFilename = DIRECTORY_SEPARATOR . $file->getBasename('yml') . 'svg';
                $expected = $this->readGraphNodeTitlesFrom($this->expectationFolder . $svgFilename);
                $result = $this->readGraphNodeTitlesFrom($this->outputFolder . $svgFilename);

                $tester->assertNotEmpty($expected, $file->getBasename());
                $tester->assertNotEmpty($result, $file->getBasename());
                $tester->assertSame($expected, $result, $file->getBasename());
            }
        }
    }

    /**
     * @param string $filepath
     * @return array
     */
    private function readGraphNodeTitlesFrom($filepath)
    {
        $xml = simplexml_load_file($filepath);
        $xml->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
        $titles = array_map('strval', $xml->xpath('/svg:svg/svg:g[1]/svg:g/svg:title'));
        sort($titles);

        return $titles;
    }
}
