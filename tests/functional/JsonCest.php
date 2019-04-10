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
    private $resultsWithViolations = ['total', 'packages', 'layers', 'complex-cycle', 'namespace-filter'];

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
        foreach ($this->dir as $file) {
            if (!$file->isDot()) {

                $target = codecept_output_dir() . 'json' . DIRECTORY_SEPARATOR . $file->getBasename('yml') . 'json';

                $cmd = $tester->getTool() . ' -q analyze ' . $file->getRealPath()
                       . ' -f "PhpDA\Writer\Strategy\Json"'
                       . ' -t ' . $target;

                \Codeception\Util\Debug::debug($cmd);
                exec($cmd, $output, $return);
                $jsonFilename = DIRECTORY_SEPARATOR . $file->getBasename('yml') . 'json';
                $expected = $this->readResultFrom($this->expectationFolder . $jsonFilename);
                $result = $this->readResultFrom($this->outputFolder . $jsonFilename);

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

    /**
     * @param string $jsonFile
     * @return array
     */
    private function readResultFrom($jsonFile)
    {
        $result = json_decode(file_get_contents($jsonFile), true);

        $result['edges'] = $this->normalizeFileLocationsIn($result['edges']);
        $result['vertices'] = $this->normalizeFileLocationsIn($result['vertices']);

        unset($result['label']);

        return $result;
    }

    /**
     * @param array $dependency
     * @return array
     */
    private function normalizeFileLocationsIn(array $dependency)
    {
        foreach ($dependency as $index => $details) {
            if (isset($details['locations'])) {
                foreach ($details['locations'] as $key => $value) {
                    $value['file'] = $this->filter($value['file']);
                    $dependency[$index]['locations'][$key] = $value;
                }
            } elseif (isset($details['location'])) {
                $dependency[$index]['location']['file'] = $this->filter($details['location']['file']);
            }
        }

        return $dependency;
    }

    /**
     * @param string $path
     * @return string
     */
    private function filter($path)
    {
        if (strpos($path, 'src') !== false) {
            $path = substr($path, strpos($path, 'src') -1);
        }

        if (strpos($path, 'Project') !== false) {
            $path = substr($path, strpos($path, 'Project') -1);
        }

        return $path;
    }
}
