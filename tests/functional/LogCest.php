<?php

class LogCest
{
    /**
     * @param FunctionalTester $tester
     */
    public function testDisplayParseErrors(FunctionalTester $tester)
    {
        $cmd = $tester->getTool() . ' analyze'
               . ' -s ' . codecept_data_dir('log' . DIRECTORY_SEPARATOR . 'PackageA')
               . ' -t ' . codecept_output_dir() . 'log.svg'
               . ' 2>&1';

        exec($cmd, $output, $return);
        $output = implode(PHP_EOL, $output);

        $tester->assertContains('Logs', $output);
        $tester->assertContains('Error', $output);
        $tester->assertContains('Warning', $output);
        $tester->assertSame(1, $return);

        unlink(codecept_output_dir() . 'log.svg');
    }
}
