<?php

class ConfigLocationCest
{
    /** @var string */
    private $rootDirectory;

    public function _before()
    {
        $this->rootDirectory = getcwd();
    }

    public function _after()
    {
        chdir($this->rootDirectory);
    }

    /**
     * @param FunctionalTester $tester
     */
    public function testUsingDefaultInWorkingDirectory(FunctionalTester $tester)
    {
        chdir(codecept_data_dir('config-location'));
        $cmd = $this->rootDirectory . '/bin/phpda -q';
        exec($cmd, $output, $return);

        $tester->assertTrue(file_exists('phpda.svg'));
        $tester->assertSame(0, $return);

        unlink('phpda.svg');
    }
}
