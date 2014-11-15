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

    public function testMutateAndAccessCallMode()
    {
        $this->assertFalse($this->fixture->isCallMode());
        $this->fixture->setCallMode();
        $this->assertTrue($this->fixture->isCallMode());
    }

    public function testMutateAndAccessLayout()
    {
        $this->assertInstanceOf('PhpDA\Layout\NullLayout', $this->fixture->getLayout());
        $layout = \Mockery::mock('PhpDA\Layout\LayoutInterface');
        $this->fixture->setLayout($layout);
        $this->assertSame($layout, $this->fixture->getLayout());
    }

    public function testAttachAnalysis()
    {
        $vertexLayout = array(1, 1);
        $vertexUnsupportedLayout = array(1, 1);
        $vertexNamespacedStringLayout = array(1, 1);
        $edgeLayout = array(2, 1);
        $edgeImplementLayout = array(2, 2);
        $edgeExtendLayout = array(2, 3);
        $edgeTraitUseLayout = array(2, 4);
        $edgeUnsupportedLayout = array(2, 5);
        $edgeNamespacedStringLayout = array(2, 6);
        $layout = \Mockery::mock('PhpDA\Layout\LayoutInterface');
        $layout->shouldReceive('getVertex')->andReturn($vertexLayout);
        $layout->shouldReceive('getVertexNamespacedString')->andReturn($vertexNamespacedStringLayout);
        $layout->shouldReceive('getVertexUnsupported')->andReturn($vertexUnsupportedLayout);
        $layout->shouldReceive('getEdge')->andReturn($edgeLayout);
        $layout->shouldReceive('getEdgeImplement')->andReturn($edgeImplementLayout);
        $layout->shouldReceive('getEdgeExtend')->andReturn($edgeExtendLayout);
        $layout->shouldReceive('getEdgeTraitUse')->andReturn($edgeTraitUseLayout);
        $layout->shouldReceive('getEdgeUnsupported')->andReturn($edgeUnsupportedLayout);
        $layout->shouldReceive('getEdgeNamespacedString')->andReturn($edgeNamespacedStringLayout);
        $this->fixture->setLayout($layout);

        $meta = \Mockery::mock('PhpDA\Entity\Meta');

        $implementedName = \Mockery::mock('PhpParser\Node\Name');
        $implementedName->shouldReceive('toString')->once()->andReturn('implementedName');
        $meta->shouldReceive('getImplementedNamespaces')->once()->andReturn(array($implementedName));

        $extendedName = \Mockery::mock('PhpParser\Node\Name');
        $extendedName->shouldReceive('toString')->once()->andReturn('extendedName');
        $meta->shouldReceive('getExtendedNamespaces')->once()->andReturn(array($extendedName));

        $usedTraitName = \Mockery::mock('PhpParser\Node\Name');
        $usedTraitName->shouldReceive('toString')->once()->andReturn('usedTraitName');
        $meta->shouldReceive('getUsedTraitNamespaces')->once()->andReturn(array($usedTraitName));

        $adt = \Mockery::mock('PhpDA\Entity\Adt');
        $adt->shouldReceive('getMeta')->andReturn($meta);

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
        $declaredNameVertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $implementedNameVertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $implementedNameVertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $extendedNameVertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $extendedNameVertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $usedTraitNameVertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $usedTraitNameVertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $usedName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $usedName1Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $usedName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $usedName2Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $unsupportedName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $unsupportedName1Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $unsupportedName1Vertex->shouldReceive('setLayout')->with($vertexUnsupportedLayout)->once()->andReturnSelf();
        $unsupportedName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $unsupportedName2Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $unsupportedName2Vertex->shouldReceive('setLayout')->with($vertexUnsupportedLayout)->once()->andReturnSelf();
        $stringName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $stringName1Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $stringName1Vertex->shouldReceive('setLayout')->with($vertexNamespacedStringLayout)->once()->andReturnSelf();
        $stringName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $stringName2Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $stringName2Vertex->shouldReceive('setLayout')->with($vertexNamespacedStringLayout)->once()->andReturnSelf();

        $this->graph->shouldReceive('createVertex')->with('declaredName', true)->andReturn($declaredNameVertex);
        $this->graph->shouldReceive('createVertex')->with('implementedName', true)->andReturn($implementedNameVertex);
        $this->graph->shouldReceive('createVertex')->with('extendedName', true)->andReturn($extendedNameVertex);
        $this->graph->shouldReceive('createVertex')->with('usedTraitName', true)->andReturn($usedTraitNameVertex);
        $this->graph->shouldReceive('createVertex')->with('usedName1', true)->andReturn($usedName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('usedName2', true)->andReturn($usedName2Vertex);
        $this->graph->shouldReceive('createVertex')->with('unsupportedName1', true)->andReturn($unsupportedName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('unsupportedName2', true)->andReturn($unsupportedName2Vertex);
        $this->graph->shouldReceive('createVertex')->with('stringName1', true)->andReturn($stringName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('stringName2', true)->andReturn($stringName2Vertex);

        $edge = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edge->shouldReceive('setLayout')->with($edgeLayout);
        $edgeExtend = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edgeExtend->shouldReceive('setLayout')->with($edgeExtendLayout);
        $edgeImplement = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edgeImplement->shouldReceive('setLayout')->with($edgeImplementLayout);
        $edgeTraitUse = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edgeTraitUse->shouldReceive('setLayout')->with($edgeTraitUseLayout);
        $edgeUnsupported = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edgeUnsupported->shouldReceive('setLayout')->with($edgeUnsupportedLayout);
        $edgeNamespaced = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edgeNamespaced->shouldReceive('setLayout')->with($edgeNamespacedStringLayout);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($implementedNameVertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($implementedNameVertex)->once()->andReturn(
            $edgeImplement
        );
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($implementedNameVertex)->andReturn(true);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($extendedNameVertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($extendedNameVertex)->once()->andReturn($edgeExtend);
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($extendedNameVertex)->andReturn(true);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($usedTraitNameVertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($usedTraitNameVertex)->once()->andReturn(
            $edgeTraitUse
        );
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($usedTraitNameVertex)->andReturn(true);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($usedName1Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($usedName1Vertex)->once()->andReturn($edge);
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($usedName2Vertex)->andReturn(true);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($unsupportedName1Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($unsupportedName1Vertex)->once()->andReturn(
            $edgeUnsupported
        );
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($unsupportedName2Vertex)->andReturn(true);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($stringName1Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($stringName1Vertex)->once()->andReturn(
            $edgeNamespaced
        );
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($stringName2Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($stringName2Vertex)->once()->andReturn(
            $edgeNamespaced
        );

        $analysis = \Mockery::mock('PhpDA\Entity\Analysis');
        $analysis->shouldReceive('getAdts')->andReturn(array($adt));
        $analysis->shouldReceive('hasParseError')->once()->andReturn(false);
        $this->fixture->attach($analysis);

        $this->assertFalse($this->fixture->hasAnalysisFailures());
        $this->assertEmpty($this->fixture->getAnalysisFailures());
    }

    public function testAttachAnalysisForCallMode()
    {
        $this->fixture->setCallMode();

        $vertexLayout = array(1, 1);
        $vertexUnsupportedLayout = array(1, 1);
        $vertexNamespacedStringLayout = array(1, 1);
        $edgeLayout = array(2, 1);
        $edgeUnsupportedLayout = array(2, 5);
        $edgeNamespacedStringLayout = array(2, 6);
        $layout = \Mockery::mock('PhpDA\Layout\LayoutInterface');
        $layout->shouldReceive('getVertex')->andReturn($vertexLayout);
        $layout->shouldReceive('getVertexNamespacedString')->andReturn($vertexNamespacedStringLayout);
        $layout->shouldReceive('getVertexUnsupported')->andReturn($vertexUnsupportedLayout);
        $layout->shouldReceive('getEdge')->andReturn($edgeLayout);
        $layout->shouldReceive('getEdgeUnsupported')->andReturn($edgeUnsupportedLayout);
        $layout->shouldReceive('getEdgeNamespacedString')->andReturn($edgeNamespacedStringLayout);
        $this->fixture->setLayout($layout);

        $adt = \Mockery::mock('PhpDA\Entity\Adt');

        $declaredName = \Mockery::mock('PhpParser\Node\Name');
        $declaredName->shouldReceive('toString')->once()->andReturn('declaredName');
        $adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declaredName);

        $calledName1 = \Mockery::mock('PhpParser\Node\Name');
        $calledName1->shouldReceive('toString')->once()->andReturn('calledName1');
        $calledName2 = \Mockery::mock('PhpParser\Node\Name');
        $calledName2->shouldReceive('toString')->once()->andReturn('calledName2');
        $adt->shouldReceive('getCalledNamespaces')->once()->andReturn(array($calledName1, $calledName2));

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
        $declaredNameVertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $calledName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $calledName1Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $calledName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $calledName2Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $unsupportedName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $unsupportedName1Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $unsupportedName1Vertex->shouldReceive('setLayout')->with($vertexUnsupportedLayout)->once()->andReturnSelf();
        $unsupportedName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $unsupportedName2Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $unsupportedName2Vertex->shouldReceive('setLayout')->with($vertexUnsupportedLayout)->once()->andReturnSelf();
        $stringName1Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $stringName1Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $stringName1Vertex->shouldReceive('setLayout')->with($vertexNamespacedStringLayout)->once()->andReturnSelf();
        $stringName2Vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $stringName2Vertex->shouldReceive('setLayout')->with($vertexLayout)->once()->andReturnSelf();
        $stringName2Vertex->shouldReceive('setLayout')->with($vertexNamespacedStringLayout)->once()->andReturnSelf();

        $this->graph->shouldReceive('createVertex')->with('declaredName', true)->andReturn($declaredNameVertex);
        $this->graph->shouldReceive('createVertex')->with('calledName1', true)->andReturn($calledName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('calledName2', true)->andReturn($calledName2Vertex);
        $this->graph->shouldReceive('createVertex')->with('unsupportedName1', true)->andReturn($unsupportedName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('unsupportedName2', true)->andReturn($unsupportedName2Vertex);
        $this->graph->shouldReceive('createVertex')->with('stringName1', true)->andReturn($stringName1Vertex);
        $this->graph->shouldReceive('createVertex')->with('stringName2', true)->andReturn($stringName2Vertex);

        $edge = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edge->shouldReceive('setLayout')->with($edgeLayout);
        $edgeUnsupported = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edgeUnsupported->shouldReceive('setLayout')->with($edgeUnsupportedLayout);
        $edgeNamespaced = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edgeNamespaced->shouldReceive('setLayout')->with($edgeNamespacedStringLayout);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($calledName1Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($calledName1Vertex)->once()->andReturn($edge);
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($calledName2Vertex)->andReturn(true);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($unsupportedName1Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($unsupportedName1Vertex)->once()->andReturn(
            $edgeUnsupported
        );
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($unsupportedName2Vertex)->andReturn(true);

        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($stringName1Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($stringName1Vertex)->once()->andReturn(
            $edgeNamespaced
        );
        $declaredNameVertex->shouldReceive('hasEdgeTo')->with($stringName2Vertex)->andReturn(false);
        $declaredNameVertex->shouldReceive('createEdgeTo')->with($stringName2Vertex)->once()->andReturn(
            $edgeNamespaced
        );

        $analysis = \Mockery::mock('PhpDA\Entity\Analysis');
        $analysis->shouldReceive('getAdts')->andReturn(array($adt));
        $analysis->shouldReceive('hasParseError')->once()->andReturn(false);
        $this->fixture->attach($analysis);

        $this->assertFalse($this->fixture->hasAnalysisFailures());
        $this->assertEmpty($this->fixture->getAnalysisFailures());
    }

    public function testAttachAnalysisWithParseError()
    {
        $file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $file->shouldReceive('getRealPath')->andReturn('foo');
        $parseError = new \PhpParser\Error('foo');
        $analysis = \Mockery::mock('PhpDA\Entity\Analysis');
        $analysis->shouldReceive('hasParseError')->once()->andReturn(true);
        $analysis->shouldReceive('getParseError')->once()->andReturn($parseError);
        $analysis->shouldReceive('getFile')->once()->andReturn($file);
        $this->fixture->attach($analysis);

        $this->assertTrue($this->fixture->hasAnalysisFailures());
        $failures = $this->fixture->getAnalysisFailures();

        foreach ($failures as $path => $error) {
            $this->assertSame($parseError, $error);
            $this->assertSame('foo', $path);
        }
    }
}
