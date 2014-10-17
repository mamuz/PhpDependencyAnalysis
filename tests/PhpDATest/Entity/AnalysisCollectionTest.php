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

namespace PhpDATest\Entity;

use PhpDA\Entity\AnalysisCollection;

class AnalysisCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var AnalysisCollection */
    protected $fixture;

    /** @var \Fhaculty\Graph\Graph | \Mockery\MockInterface */
    protected $graph;

    protected function setUp()
    {
        $this->graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $this->fixture = new AnalysisCollection($this->graph);
    }

    public function testAccessGraph()
    {
        $this->assertSame($this->graph, $this->fixture->getGraph());
    }

    public function testAttachAnalysis()
    {
        $adt = \Mockery::mock('PhpDA\Entity\Adt');

        $declaredName = \Mockery::mock('PhpParser\Node\Name');
        $declaredName->shouldReceive('toString')->once()->andReturn('declaredName');
        $adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declaredName);

        $usedName1 = \Mockery::mock('PhpParser\Node\Name');
        $usedName1->shouldReceive('toString')->once()->andReturn('usedName1');
        $usedName2 = \Mockery::mock('PhpParser\Node\Name');
        $usedName2->shouldReceive('toString')->once()->andReturn('usedName2');
        $adt->shouldReceive('getUsedNamespaces')->once()->andReturn(array($usedName1, $usedName2));

        $unsupportedName1 = \Mockery::mock('PhpParser\Node\Name');
        $unsupportedName1->shouldReceive('toString')->once()->andReturn('unsupportedName1');
        $unsupportedName2 = \Mockery::mock('PhpParser\Node\Name');
        $unsupportedName2->shouldReceive('toString')->once()->andReturn('unsupportedName2');
        $adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array($unsupportedName1, $unsupportedName2));

        $stringName1 = \Mockery::mock('PhpParser\Node\Name');
        $stringName1->shouldReceive('toString')->once()->andReturn('stringName1');
        $stringName2 = \Mockery::mock('PhpParser\Node\Name');
        $stringName2->shouldReceive('toString')->once()->andReturn('stringName2');
        $adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array($stringName1, $stringName2));

        $declaredNameVertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $usedName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $usedName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $unsupportedName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $unsupportedName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $stringName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $stringName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $this->graph->shouldReceive('createVertex')->with('declaredName', true)->andReturn($declaredNameVertex);
        $this->graph->shouldReceive('createVertex')->with('usedName1', true)->andReturn($usedName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('usedName2', true)->andReturn($usedName2Vertex);
        $this->graph->shouldReceive('createVertex')->with('unsupportedName1', true)->andReturn($unsupportedName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('unsupportedName2', true)->andReturn($unsupportedName2Vertex);
        $this->graph->shouldReceive('createVertex')->with('stringName1', true)->andReturn($stringName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('stringName2', true)->andReturn($stringName2Vertex);

        $usedName1Vertex->shouldReceive('hasEdgeTo')->with($declaredNameVertex)->andReturn(false);
        $usedName1Vertex->shouldReceive('createEdgeTo')->with($declaredNameVertex)->once();
        $usedName2Vertex->shouldReceive('hasEdgeTo')->with($declaredNameVertex)->andReturn(true);

        $unsupportedName1Vertex->shouldReceive('hasEdgeTo')->with($declaredNameVertex)->andReturn(false);
        $unsupportedName1Vertex->shouldReceive('createEdgeTo')->with($declaredNameVertex)->once();
        $unsupportedName2Vertex->shouldReceive('hasEdgeTo')->with($declaredNameVertex)->andReturn(true);

        $stringName1Vertex->shouldReceive('hasEdgeTo')->with($declaredNameVertex)->andReturn(false);
        $stringName1Vertex->shouldReceive('createEdgeTo')->with($declaredNameVertex)->once();
        $stringName2Vertex->shouldReceive('hasEdgeTo')->with($declaredNameVertex)->andReturn(false);
        $stringName2Vertex->shouldReceive('createEdgeTo')->with($declaredNameVertex)->once();

        $analysis = \Mockery::mock('PhpDA\Entity\Analysis');
        $analysis->shouldReceive('getAdts')->andReturn(array($adt));
        $this->fixture->attach($analysis);
    }
}
