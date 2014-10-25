<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDATest\Writer\Strategy;

class AbstractStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PhpDA\Writer\Strategy\AbstractStrategy */
    protected $fixture;

    /** @var string */
    protected $output = 'foo';

    protected function setUp()
    {
        $this->fixture = $this->getMockForAbstractClass('PhpDA\Writer\Strategy\AbstractStrategy');
        $this->fixture->expects($this->any())
            ->method('createOutput')
            ->willReturn($this->output);
    }

    public function testMutateAndAccessGraphCreationCallback()
    {
        $this->assertTrue(is_callable($this->fixture->getGraphCreationCallback()));
        $callback = function () {
        };
        $this->fixture->setGraphCreationCallback($callback);
        $this->assertSame($callback, $this->fixture->getGraphCreationCallback());
    }

    public function testInvalidMutationOfGraphCreationCallback()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->fixture->setGraphCreationCallback('foo');
    }

    public function testFilter()
    {
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $analysisCollection = \Mockery::mock('PhpDA\Entity\AnalysisCollection');
        $analysisCollection->shouldReceive('getGraph')->once()->andReturn($graph);

        $this->assertSame($this->output, $this->fixture->filter($analysisCollection));
    }

    public function testFilterWithInvalidGraphCreationCallback()
    {
        $this->setExpectedException('RuntimeException');

        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $analysisCollection = \Mockery::mock('PhpDA\Entity\AnalysisCollection');
        $analysisCollection->shouldReceive('getGraph')->once()->andReturn($graph);
        $callback = function () {
        };
        $this->fixture->setGraphCreationCallback($callback);

        $this->assertSame($this->output, $this->fixture->filter($analysisCollection));
    }
}
