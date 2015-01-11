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

namespace PhpDATest\Layout\Helper;

use PhpDA\Layout\Helper\CycleDetector;

class CycleDetectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var CycleDetector */
    protected $fixture;

    /** @var \PhpDA\Layout\Helper\DependencyMapGenerator | \Mockery\MockInterface */
    protected $mapGenerator;

    /** @var \Fhaculty\Graph\Graph | \Mockery\MockInterface */
    protected $graph;

    /** @var array */
    protected $map = array(
        'A' => array('B'),
        'B' => array('C'),
        'C' => array('D'),
        'D' => array('A', 'E'),
        'F' => array('C', 'A'),
    );

    protected function setUp()
    {
        $vertices = \Mockery::mock('Fhaculty\Graph\Set\Vertices');
        $vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $vertex->shouldReceive('getId')->andReturn(1);
        $vertex->shouldReceive('getVerticesEdge')->andReturn($vertices);
        $vertices->shouldReceive('getMap')->andReturn(array($vertex));
        $this->graph = \Mockery::mock('Fhaculty\Graph\Graph')->shouldIgnoreMissing();
        $this->graph->shouldReceive('getVertices')->andReturn($vertices);
        $this->graph->shouldReceive('createGraphCloneVertices')->andReturn($this->graph);

        $this->mapGenerator = \Mockery::mock('PhpDA\Layout\Helper\DependencyMapGenerator');
        $this->mapGenerator->shouldReceive('buildBy')->with($this->graph)->andReturn($this->map);

        $this->fixture = new CycleDetector($this->mapGenerator);
    }

    public function testFindCycles()
    {
        $cycles = $this->fixture->inspect($this->graph)->getCycles();
        foreach ($cycles as $cycle) {
            $this->assertSame('A -> B -> C -> D -> A', $cycle->toString());
        }
    }

    public function testFindCycledEdges()
    {
        $expected = array('foo');
        $edges = \Mockery::mock('Fhaculty\Graph\Set\Edges');
        $edges->shouldReceive('getEdgesMatch')->andReturn($expected);
        $this->graph->shouldReceive('getEdges')->andReturn($edges);

        $cycledEdges = $this->fixture->inspect($this->graph)->getCycledEdges();

        $this->assertSame($expected, $cycledEdges);
    }
}
