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

    public function _failed()
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

    /**
     * @param FunctionalTester $tester
     */
    public function testUsingDefaultInWorkingDirectoryWithDefaultConfig(FunctionalTester $tester)
    {
        chdir(codecept_data_dir('default-config'));
        $cmd = $this->rootDirectory . '/bin/phpda -q';
        exec($cmd, $output, $return);

        $tester->assertTrue(file_exists('phpda.svg'));
        $tester->assertSame(0, $return);

        unlink('phpda.svg');
    }


    /**
     * @param FunctionalTester $tester
     */
    public function testUsingDefaultInWorkingDirectoryWithLocalConfig(FunctionalTester $tester)
    {
        chdir(codecept_data_dir('config-location'));
        $cmd = $this->rootDirectory . '/bin/phpda -q analyze ./custom.yml';
        exec($cmd, $output, $return);

        $tester->assertTrue(file_exists('custom.svg'));
        $tester->assertSame(0, $return);

        unlink('custom.svg');
    }
}
