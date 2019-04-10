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

namespace PhpDATest\Writer\Extractor;

use PhpDA\Writer\Extractor\Graph;

class GraphTest extends \PHPUnit_Framework_TestCase
{
    /** @var Graph */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Graph;
    }

    public function testExtraction()
    {
        $expected = array(
            'edges'    => array(
                'start=>end' => array(
                    'from'                       => 'start',
                    'to'                         => 'end',
                    'locations'                  => array('location'),
                    'belongsToCycle'             => true,
                    'referenceValidatorMessages' => array('referenceMessages'),
                ),
            ),
            'vertices' => array(
                'end'   => array(
                    'name'        => 'end',
                    'usedByCount' => 2,
                    'adt'         => array('adt'),
                    'location'    => 'location',
                    'group'       => 'group',
                ),
                'start' => array(
                    'name'        => 'start',
                    'usedByCount' => 2,
                    'adt'         => array('adt'),
                    'location'    => 'location',
                    'group'       => 'group',
                ),
            ),
            'cycles'   => array('cycle'),
            'groups'   => array('groups'),
            'log'      => array('logentries'),
            'label'    => 'label',
        );

        $edges = \Mockery::mock('Fhaculty\Graph\Set\Edges');
        $edges->shouldReceive('count')->andReturn(2);

        $location = \Mockery::mock('PhpDA\Entity\Location');
        $location->shouldReceive('toArray')->andReturn('location');

        $vertexStart = \Mockery::mock('Fhaculty\Graph\Vertex');
        $vertexStart->shouldReceive('getId')->andReturn('start');
        $vertexStart->shouldReceive('getGroup')->andReturn('group');
        $vertexStart->shouldReceive('getEdgesIn')->andReturn($edges);
        $vertexStart->shouldReceive('getAttribute')->with('adt', array())->andReturn(array('adt'));
        $vertexStart->shouldReceive('getAttribute')->with('locations', array())->andReturn(array($location));

        $vertexEnd = \Mockery::mock('Fhaculty\Graph\Vertex');
        $vertexEnd->shouldReceive('getId')->andReturn('end');
        $vertexEnd->shouldReceive('getGroup')->andReturn('group');
        $vertexEnd->shouldReceive('getEdgesIn')->andReturn($edges);
        $vertexEnd->shouldReceive('getAttribute')->with('adt', array())->andReturn(array('adt'));
        $vertexEnd->shouldReceive('getAttribute')->with('locations', array())->andReturn(array($location));

        $edge = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edge->shouldReceive('getVertexStart')->andReturn($vertexStart);
        $edge->shouldReceive('getVertexEnd')->andReturn($vertexEnd);
        $edge->shouldReceive('getAttribute')->with('locations', array())->andReturn(array($location));
        $edge->shouldReceive('getAttribute')->with('belongsToCycle', false)->andReturn(true);
        $edge->shouldReceive('getAttribute')->with('referenceValidatorMessages')->andReturn(array('referenceMessages'));

        $cycle = \Mockery::mock('PhpDA\Entity\Cycle');
        $cycle->shouldReceive('toArray')->andReturn('cycle');

        $graph = \Mockery::mock('Fhaculty\Graph\Graph');

        $graph->shouldReceive('getAttribute')->with('graphviz.groups', array())->andReturn($expected['groups']);
        $graph->shouldReceive('getAttribute')->with('graphviz.graph.label')->andReturn($expected['label']);
        $graph->shouldReceive('getAttribute')->with('cycles', array())->andReturn(array($cycle));
        $graph->shouldReceive('getAttribute')->with('logEntries', array())->andReturn(array('logentries'));

        $graph->shouldReceive('getEdges')->once()->andReturn(array($edge));

        self::assertSame($expected, $this->fixture->extract($graph));
    }
}
