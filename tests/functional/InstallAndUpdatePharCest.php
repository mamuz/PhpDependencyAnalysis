<?php

/**
 * @todo test Update and rollback
 */
class InstallAndUpdatePharCest
{
    public function _before()
    {
        copy('./download/phpda.pubkey', codecept_output_dir() . 'phpda.pubkey');
        copy('./download/phpda', codecept_output_dir() . 'phpda');
        exec('chmod +x ' . codecept_output_dir() . 'phpda');
    }

    public function _after()
    {
        $this->reset();
    }

    public function _failed()
    {
        $this->reset();
    }

    private function reset()
    {
        @unlink(codecept_output_dir() . 'phpda.pubkey');
        @unlink(codecept_output_dir() . 'phpda');
    }

    /**
     * @param FunctionalTester $tester
     */
    public function testInstall(FunctionalTester $tester)
    {
        exec(codecept_output_dir() . 'phpda --version', $output, $return);
        $output = implode(PHP_EOL, $output);

        $tester->assertContains('PhpDependencyAnalysis', $output);
        $tester->assertSame(0, $return);
    }

    /**
     * @param FunctionalTester $tester
     */
    public function testUpdateNotNeeded(FunctionalTester $tester)
    {
        // @todo
        exec(codecept_output_dir() . 'phpda update 2>&1', $output, $return);
        $output = implode(PHP_EOL, $output);

        $tester->assertContains('failed to open stream', $output);
        $tester->assertSame(1, $return);
    }

    /**
     * @param FunctionalTester $tester
     */
    public function testInstallWithoutSslKey(FunctionalTester $tester)
    {
        unlink(codecept_output_dir() . 'phpda.pubkey');

        exec(codecept_output_dir() . 'phpda --version 2>&1', $output, $return);
        $output = implode(PHP_EOL, $output);

        $tester->assertContains('openssl signature could not be verified', $output);
        $tester->assertSame(255, $return);
    }
}
