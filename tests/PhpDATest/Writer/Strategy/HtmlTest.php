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

use Fhaculty\Graph\Graph;
use PhpDA\Writer\Strategy\Html;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /** @var Html */
    protected $fixture;

    /** @var string */
    protected $output = 'foo';

    /** @var \Fhaculty\Graph\GraphViz | \Mockery\MockInterface */
    protected $graphViz = 'foo';

    protected function setUp()
    {
        $mock = $this->graphViz = \Mockery::mock('Fhaculty\Graph\GraphViz');
        $callback = function (Graph $graph) use ($mock) {
            return $mock;
        };
        $this->fixture = new Html;
        $this->fixture->setGraphCreationCallback($callback);
    }

    public function testMutateAndAccessImagePlaceholder()
    {
        $this->assertSame('{GRAPH_IMAGE}', $this->fixture->getImagePlaceholder());
        $this->fixture->setImagePlaceholder('foo');
        $this->assertSame('foo', $this->fixture->getImagePlaceholder());
    }

    public function testMutateAndAccessTemplate()
    {
        $this->assertSame('<html><body>{GRAPH_IMAGE}</body></html>', $this->fixture->getTemplate());
        $this->fixture->setTemplate('foo');
        $this->assertSame('foo', $this->fixture->getTemplate());
    }

    public function testFilter()
    {
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $analysisCollection = \Mockery::mock('PhpDA\Entity\AnalysisCollection');
        $analysisCollection->shouldReceive('getGraph')->once()->andReturn($graph);

        $this->graphViz->shouldReceive('setFormat')->once()->with('svg')->andReturnSelf();
        $this->graphViz->shouldReceive('createImageHtml')->once()->andReturn($this->output);

        $this->assertSame(
            '<html><body>' . $this->output . '</body></html>',
            $this->fixture->filter($analysisCollection)
        );
    }
}
