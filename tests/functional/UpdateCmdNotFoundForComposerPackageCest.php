<?php

class UpdateCmdNotFoundForComposerPackageCest
{
    /**
     * @param FunctionalTester $tester
     */
    public function testExceptionMsg(FunctionalTester $tester)
    {
        $cmd = './bin/phpda update 2>&1';

        exec($cmd, $output, $return);
        $output = implode(PHP_EOL, $output);

        $tester->assertContains('Command "update" is not defined', $output);
        $tester->assertSame(1, $return);
    }
}
