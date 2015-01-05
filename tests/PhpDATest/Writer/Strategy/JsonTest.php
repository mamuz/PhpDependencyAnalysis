<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Marco Muths
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

use PhpDA\Writer\Strategy\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    /** @var Json */
    protected $fixture;

    /** @var string */
    protected $output = '{"ClassA":["DependencyA","DependencyB"]}';

    protected function setUp()
    {
        $this->fixture = new Json;
    }

    public function testFilter()
    {
        $graphViz = \Mockery::mock('PhpDA\Layout\GraphViz');
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $vertexFrom = \Mockery::mock('Fhaculty\Graph\Vertex');
        //The following line is a workaround on mocking Fhaculty\Graph\Set\Vertices as the file has errors (e.g. usage
        //of a never defined constant) which break the PhpReflection classes and thus break Mockery.
        $vertexToSet = \Mockery::mock('vertices');
        $vertexTo1 = \Mockery::mock('Fhaculty\Graph\Vertex');
        $vertexTo2 = \Mockery::mock('Fhaculty\Graph\Vertex');

        $graphViz->shouldReceive('getGraph')->once()->andReturn($graph);
        $graph->shouldReceive('getVertices')->once()->andReturn(array($vertexFrom));
        $vertexFrom->shouldReceive('getVerticesEdgeTo')->once()->andReturn($vertexToSet);
        $vertexToSet->shouldReceive('getVerticesDistinct')->once()->andReturn(array($vertexTo1, $vertexTo2));
        $vertexFrom->shouldReceive('getId')->times(2)->andReturn("ClassA");
        $vertexTo1->shouldReceive('getId')->once()->andReturn("DependencyA");
        $vertexTo2->shouldReceive('getId')->once()->andReturn("DependencyB");

        $this->assertSame($this->output, $this->fixture->filter($graphViz));
    }
}
