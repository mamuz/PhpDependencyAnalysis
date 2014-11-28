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

namespace PhpDATest\Layout;

use PhpDA\Entity\AnalysisCollection;
use PhpDA\Layout\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Builder */
    protected $fixture;

    /** @var \PhpDA\Layout\Helper\GroupGenerator | \Mockery\MockInterface */
    protected $groupGenerator;

    /** @var \PhpDA\Layout\GraphViz | \Mockery\MockInterface */
    protected $graphViz;

    /** @var \PhpDA\Layout\Graph | \Mockery\MockInterface */
    protected $graph;

    /** @var \PhpParser\Node\Name | \Mockery\MockInterface */
    protected $name;

    protected function setUp()
    {
        $this->name = \Mockery::mock('PhpParser\Node\Name');
        $this->groupGenerator = \Mockery::mock('PhpDA\Layout\Helper\GroupGenerator');
        $this->graph = \Mockery::mock('PhpDA\Layout\Graph');
        $this->graph->shouldReceive('setLayout')->with(array());
        $this->graphViz = \Mockery::mock('PhpDA\Layout\GraphViz');
        $this->graphViz->shouldReceive('getGraph')->andReturn($this->graph);

        $this->fixture = new Builder($this->graphViz, $this->groupGenerator);
    }

    /**
     * @param array $parts
     * @return \PhpParser\Node\Name | \Mockery\MockInterface
     */
    private function createName($parts = array())
    {
        $name = clone $this->name;
        $name->shouldReceive('toString')->andReturn(implode('\\', $parts));
        $name->parts = $parts;

        return $name;
    }

    public function testDelegatingGroupLengthMutatorToGroupGenerator()
    {
        $this->groupGenerator->shouldReceive('setGroupLength')->once()->with(4);
        $this->fixture->setGroupLength(4);
    }

    public function testFluentInterfaceForCreating()
    {
        $this->groupGenerator->shouldReceive('getGroups')->andReturn(array());
        $this->graph->shouldReceive('setLayout');
        $this->graphViz->shouldReceive('setGroups');
        $this->graphViz->shouldReceive('setGroupLayout');

        $this->assertSame($this->fixture, $this->fixture->create());
    }

    public function testDelegatingGraphLayoutGeneratedGroups()
    {
        $layout = \Mockery::mock('PhpDA\Layout\LayoutInterface');
        $layout->shouldReceive('getGraph')->once()->andReturn(array('foo'));
        $layout->shouldReceive('getGroup')->once()->andReturn(array('bar'));

        $this->groupGenerator->shouldReceive('getGroups')->andReturn(array('baz'));

        $this->graph->shouldReceive('setLayout')->once()->with(array('foo'));
        $this->graphViz->shouldReceive('setGroups')->once()->with(array('baz'));
        $this->graphViz->shouldReceive('setGroupLayout')->once()->with(array('bar'));

        $this->fixture->setLayout($layout);

        $this->assertSame($this->fixture, $this->fixture->create());
    }

    public function testAccessGraphViz()
    {
        $this->assertSame($this->graphViz, $this->fixture->getGraphViz());
    }

    public function testDependencyCreationInCallMode()
    {
        $this->fixture->setCallMode();

        $layout = \Mockery::mock('PhpDA\Layout\LayoutInterface');
        $layout->shouldReceive('getGraph')->andReturn(array());
        $layout->shouldReceive('getGroup')->andReturn(array());
        $layout->shouldReceive('getEdge')->andReturn(array());
        $layout->shouldReceive('getEdgeUnsupported')->andReturn(array());
        $layout->shouldReceive('getEdgeNamespacedString')->andReturn(array());
        $layout->shouldReceive('getVertexUnsupported')->andReturn(array());
        $layout->shouldReceive('getVertexNamespacedString')->andReturn(array());
        $layout->shouldReceive('getVertex')->andReturn(array('vertex'));
        $this->fixture->setLayout($layout);
        $this->groupGenerator->shouldReceive('getGroups')->andReturn(array());
        $this->graph->shouldReceive('setLayout');
        $this->graphViz->shouldReceive('setGroups');
        $this->graphViz->shouldReceive('setGroupLayout');

        $declared = $this->createName(array('dec', 'name'));
        $vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $adt = \Mockery::mock('PhpDA\Entity\Adt');
        $adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declared);
        $this->graph->shouldReceive('createVertex')->once()->with('dec\\name', true)->andReturn($vertex);
        $this->groupGenerator->shouldReceive('getIdFor')->once()->with($declared)->andReturn(5);
        $vertex->shouldReceive('setGroup')->once()->with(5);
        $vertex->shouldReceive('setLayout')->once()->with(array('vertex', 'group' => 5));

        $adt->shouldReceive('getImplementedNamespaces')->never();
        $adt->shouldReceive('getExtendedNamespaces')->never();
        $adt->shouldReceive('getUsedTraitNamespaces')->never();
        $adt->shouldReceive('getUsedNamespaces')->never();

        $adt->shouldReceive('getCalledNamespaces')->once()->andReturn(array());
        $adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array());
        $adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array());

        $collection = new AnalysisCollection;
        $analysis = \Mockery::mock('PhpDA\Entity\Analysis');
        $analysis->shouldReceive('getAdts')->andReturn(array($adt));
        $collection->attach($analysis);

        $this->fixture->setAnalysisCollection($collection);

        $this->assertSame($this->fixture, $this->fixture->create());
    }
}
