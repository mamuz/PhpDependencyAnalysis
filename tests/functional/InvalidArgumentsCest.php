<?php

class InvalidArgumentsCest
{
    /** @var array */
    private $invalidArguments = [
        'mode'        => [
            'longsyntax'    => '--mode=foo',
            'shortsyntax'   => '-m foo',
            'expectedError' => 'mode must be "usage" or "call" or "inheritance"',
        ],
        'source'      => [
            'longsyntax'    => '--source=foo',
            'shortsyntax'   => '-s foo',
            'expectedError' => 'directory does not exist',
        ],
        'filePattern' => [
            'longsyntax'    => '--filePattern=...',
            'shortsyntax'   => '-p ...',
            'expectedError' => 'unknown modifier',
        ],
        'formatter'   => [
            'longsyntax'    => '--formatter=foo',
            'shortsyntax'   => '-f foo',
            'expectedError' => 'does not exist',
        ],
        'target'      => [
            'longsyntax'    => '--target=.',
            'shortsyntax'   => '-t .',
            'expectedError' => 'failed to open stream',
        ],
    ];

    /**
     * @param FunctionalTester $tester
     */
    public function testInvokeInvalidArguments(FunctionalTester $tester)
    {
        foreach ($this->invalidArguments as $argument) {
            $this->failWith($tester, $argument['longsyntax'], $argument['expectedError']);
            $this->failWith($tester, $argument['shortsyntax'], $argument['expectedError']);
        }
    }

    /**
     * @param FunctionalTester $tester
     * @param string           $argument
     * @param string           $error
     */
    private function failWith(FunctionalTester $tester, $argument, $error)
    {
        exec($tester->getTool() . ' -q analyze ' . $argument . ' 2>&1', $output, $return);

        $tester->assertContains($error, strtolower(implode(PHP_EOL, $output)));
        $tester->assertSame(2, $return);
    }
}
