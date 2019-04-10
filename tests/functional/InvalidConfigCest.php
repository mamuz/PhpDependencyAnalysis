<?php

class InvalidConfigCest
{
    /** @var array */
    private $invalidConfigs = [
        'mode'               => 'mode must be "usage" or "call" or "inheritance"',
        'source'             => 'directory does not exist',
        'filePattern'        => 'filepattern must be a string',
        'formatter'          => 'does not exist',
        'target'             => 'failed to open',
        'groupLength'        => 'must be an integer',
        'namespaceFilter'    => 'does not exist',
        'referenceValidator' => 'does not exist',
        'visitor'            => 'does not exist',
       // 'visitorOptions'     => 'delimiter must', @todo
    ];

    /**
     * @param FunctionalTester $tester
     */
    public function testFailing(FunctionalTester $tester)
    {
        foreach ($this->invalidConfigs as $config => $error) {
            $configfile = codecept_data_dir('invalid-configs') . DIRECTORY_SEPARATOR . $config . '.yml';
            exec($tester->getTool() . ' -q analyze ' . $configfile . ' 2>&1', $output, $return);

            $tester->assertContains($error, strtolower(implode(PHP_EOL, $output)));
            $tester->assertSame(2, $return);
            unset($output);
        }
    }
}
