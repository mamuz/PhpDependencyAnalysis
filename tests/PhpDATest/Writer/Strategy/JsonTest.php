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

use PhpDA\Writer\Strategy\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    /** @var Script */
    protected $fixture;

    /** @var string */
    protected $output = '{"ClassA":["DependencyA","DependencyB"]}';

    protected function setUp()
    {
        $this->fixture = new Json;
    }

    public function testFilter()
    {
        $analysisCollection = \Mockery::mock('PhpDA\Entity\AnalysisCollection');
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $vertex_from = \Mockery::mock('Fhaculty\Graph\Vertex');
        //The following line is a workaround on mocking Fhaculty\Graph\Set\Vertices as the file has errors (e.g. usage
        //of a never defined constant) which break the PhpReflection classes and thus break Mockery.
        $vertex_to_set = \Mockery::mock('vertices');
        $vertex_to1 = \Mockery::mock('Fhaculty\Graph\Vertex');
        $vertex_to2 = \Mockery::mock('Fhaculty\Graph\Vertex');

        $analysisCollection->shouldReceive('getGraph')->once()->andReturn($graph);
        $graph->shouldReceive('getVertices')->once()->andReturn(array($vertex_from));
        $vertex_from->shouldReceive('getVerticesEdgeTo')->once()->andReturn($vertex_to_set);
        $vertex_to_set->shouldReceive('getVerticesDistinct')->once()->andReturn(array($vertex_to1, $vertex_to2));
        $vertex_from->shouldReceive('getId')->times(2)->andReturn("ClassA");
        $vertex_to1->shouldReceive('getId')->once()->andReturn("DependencyA");
        $vertex_to2->shouldReceive('getId')->once()->andReturn("DependencyB");

        $this->assertSame($this->output, $this->fixture->filter($analysisCollection));
    }
}
