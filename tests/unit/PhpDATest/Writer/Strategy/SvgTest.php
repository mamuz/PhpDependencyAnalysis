<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 Marco Muths
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

use PhpDA\Writer\Strategy\Svg;

class SvgTest extends \PHPUnit_Framework_TestCase
{
    /** @var Svg */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Svg;
    }

    public function testFilter()
    {
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $graph->shouldReceive('getAttribute')->andReturn(null);

        $graphViz = \Mockery::mock('PhpDA\Layout\GraphViz');
        $graphViz->shouldReceive('setFormat')->once()->with('svg')->andReturnSelf();
        $graphViz->shouldReceive('createImageData')->once()->with($graph)->andReturn('foo');

        $this->fixture->setGraphViz($graphViz);

        self::assertSame('foo', $this->fixture->filter($graph));
    }
}
