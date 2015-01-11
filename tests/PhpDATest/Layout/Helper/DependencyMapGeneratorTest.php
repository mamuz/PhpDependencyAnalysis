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

use PhpDA\Layout\Helper\DependencyMapGenerator;

class DependencyMapGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var DependencyMapGenerator */
    protected $fixture;

    /** @var \Fhaculty\Graph\Graph | \Mockery\MockInterface */
    protected $graph;

    protected function setUp()
    {
        $edge12 = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edge13 = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edge23 = \Mockery::mock('Fhaculty\Graph\Edge\Directed');

        $vertex1 = \Mockery::mock('Fhaculty\Graph\Vertex');
        $vertex2 = \Mockery::mock('Fhaculty\Graph\Vertex');
        $vertex3 = \Mockery::mock('Fhaculty\Graph\Vertex');

        $edge12->shouldReceive('getVertexEnd')->once()->andReturn($vertex2);
        $edge13->shouldReceive('getVertexEnd')->once()->andReturn($vertex3);
        $edge23->shouldReceive('getVertexEnd')->once()->andReturn($vertex3);

        $vertex1->shouldReceive('getEdgesOut')->once()->andReturn(array($edge12, $edge13));
        $vertex2->shouldReceive('getEdgesOut')->once()->andReturn(array($edge23));
        $vertex3->shouldReceive('getEdgesOut')->once()->andReturn(array());

        $vertex1->shouldReceive('getId')->andReturn('A');
        $vertex2->shouldReceive('getId')->andReturn('B');
        $vertex3->shouldReceive('getId')->andReturn('C');

        $vertices = array($vertex1, $vertex2, $vertex3);

        $this->graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $this->graph->shouldReceive('getVertices')->once()->andReturn($vertices);

        $this->fixture = new DependencyMapGenerator;
    }

    public function testBuildingMap()
    {
        $expected = array(
            'A' => array('B'),
            'B' => array(),
        );

        $this->assertSame($expected, $this->fixture->buildBy($this->graph));
    }
}
