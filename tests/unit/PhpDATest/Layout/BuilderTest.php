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

namespace PhpDATest\Layout;

use PhpDA\Entity\AnalysisCollection;
use PhpDA\Layout\Builder;
use PhpParser\Node\Name;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    const LE = '_e_';

    const LEIM = '_eIm_';

    const LEEX = '_eEx_';

    const LETU = '_eTu_';

    const LEUS = '_eUs_';

    const LENS = '_eNs_';

    const LEIV = '_eiv_';

    const LEC = '_ec_';

    const LV = '_v_';

    const LVUS = '_vUs_';

    const LVNS = '_vNs_';

    /** @var Builder */
    protected $fixture;

    /** @var \PhpDA\Layout\Helper\GroupGenerator | \Mockery\MockInterface */
    protected $groupGenerator;

    /** @var \PhpDA\Layout\Helper\CycleDetector | \Mockery\MockInterface */
    protected $cycleDetector;

    /** @var \Fhaculty\Graph\Graph | \Mockery\MockInterface */
    protected $graph;

    /** @var \PhpDA\Entity\Adt | \Mockery\MockInterface */
    protected $adt;

    /** @var \Fhaculty\Graph\Vertex | \Mockery\MockInterface */
    protected $rootVertex;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    protected function setUp()
    {
        $this->groupGenerator = \Mockery::mock('PhpDA\Layout\Helper\GroupGenerator');
        $this->graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $this->cycleDetector = \Mockery::mock('PhpDA\Layout\Helper\CycleDetector');
        $this->cycleDetector->shouldReceive('inspect')->with($this->graph)->andReturnSelf();
        $this->cycleDetector->shouldReceive('getCycles')->andReturn(array());
        $this->graph->shouldReceive('setAttribute')->with('cycles', array());

        $this->adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->adt->shouldReceive('toArray')->andReturn(array());

        $this->fixture = new Builder($this->graph, $this->groupGenerator, $this->cycleDetector);
    }

    public function testDelegatingGroupLengthMutatorToGroupGenerator()
    {
        $this->groupGenerator->shouldReceive('setGroupLength')->once()->with(4);
        $this->fixture->setGroupLength(4);
    }

    public function testFluentInterfaceForCreating()
    {
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array());
        $this->groupGenerator->shouldReceive('getGroups')->andReturn(array());
        $this->graph->shouldReceive('setAttribute');

        self::assertSame($this->fixture, $this->fixture->create());
    }

    public function testDelegatingLogEntriesAndGraphLayoutGeneratedGroups()
    {
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array());
        $layout = \Mockery::mock('PhpDA\Layout\LayoutInterface');
        $layout->shouldReceive('getGraph')->once()->andReturn(array('bar' => 'foo'));
        $layout->shouldReceive('getGroup')->once()->andReturn(array('bar'));

        $this->groupGenerator->shouldReceive('getGroups')->once()->andReturn(array('baz'));

        $this->graph->shouldReceive('setAttribute')->once()->with('graphviz.graph.bar', 'foo');
        $this->graph->shouldReceive('setAttribute')->once()->with('graphviz.groups', array('baz'));
        $this->graph->shouldReceive('setAttribute')->once()->with('graphviz.groupLayout', array('bar'));
        $this->graph->shouldReceive('setAttribute')->once()->with('logEntries', array('baz'));

        $this->fixture->setLayout($layout);
        $this->fixture->setLogEntries(array('baz'));

        self::assertSame($this->fixture, $this->fixture->create());
    }

    public function testAccessGraph()
    {
        self::assertSame($this->graph, $this->fixture->getGraph());
    }

    private function prepareDependencyCreation()
    {
        $this->groupGenerator->shouldReceive('getGroups')->andReturn(array());
        $this->graph->shouldReceive('setAttribute');

        $layout = \Mockery::mock('PhpDA\Layout\LayoutInterface');
        $layout->shouldReceive('getGraph')->andReturn(array());
        $layout->shouldReceive('getGroup')->andReturn(array());

        $layout->shouldReceive('getVertex')->andReturn(array(self::LV));
        $layout->shouldReceive('getVertexUnsupported')->andReturn(array(self::LVUS));
        $layout->shouldReceive('getVertexNamespacedString')->andReturn(array(self::LVNS));

        $layout->shouldReceive('getEdge')->andReturn(array(self::LE));
        $layout->shouldReceive('getEdgeImplement')->andReturn(array(self::LEIM));
        $layout->shouldReceive('getEdgeExtend')->andReturn(array(self::LEEX));
        $layout->shouldReceive('getEdgeTraitUse')->andReturn(array(self::LETU));
        $layout->shouldReceive('getEdgeUnsupported')->andReturn(array(self::LEUS));
        $layout->shouldReceive('getEdgeNamespacedString')->andReturn(array(self::LENS));
        $layout->shouldReceive('getEdgeInvalid')->andReturn(array(self::LEIV));
        $layout->shouldReceive('getEdgeCycle')->andReturn(array(self::LEC));

        $this->fixture->setLayout($layout);

        $collection = new AnalysisCollection;
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $analysis = \Mockery::mock('PhpDA\Entity\Analysis');
        $analysis->shouldReceive('getFile')->andReturn($this->file);
        $analysis->shouldReceive('getAdts')->andReturn(array($this->adt));
        $collection->attach($analysis);

        $this->fixture->setAnalysisCollection($collection);
    }

    /**
     * @param string                        $fqcn
     * @param Name | \Mockery\MockInterface $root
     * @param bool                          $hasEdge
     * @return Name | \Mockery\MockInterface
     */
    private function createName($fqcn, Name $root = null, $hasEdge = false)
    {
        /** @var Name | \Mockery\MockInterface $name */
        $name = \Mockery::mock('PhpParser\Node\Name');
        $name->shouldReceive('getAttributes')->andReturn(
            array('startLine' => 12, 'endLine' => 14, 'fqcn' => 'Foo\\Bar')
        );
        $name->shouldReceive('toString')->andReturn($fqcn);
        $name->parts = explode('\\', $fqcn);
        $vertex = \Mockery::mock('Fhaculty\Graph\Vertex');
        $this->graph->shouldReceive('createVertex')->once()->with($fqcn, true)->andReturn($vertex);
        $this->groupGenerator->shouldReceive('getIdFor')->once()->with($name)->andReturn(5);
        $vertex->shouldReceive('setGroup')->once()->with(5);
        $vertex->shouldReceive('setAttribute');
        $vertex->shouldReceive('getAttribute');

        if ($root) {
            if ($root->parts !== $name->parts) {
                $edge = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
                $edge->shouldReceive('setAttribute');
                $edge->shouldReceive('getAttribute');
                $edge->shouldReceive('isConnection')->once()->with($this->rootVertex, $vertex)->andReturn($hasEdge);
                $this->rootVertex->shouldReceive('getEdges')->once()->andReturn(array($edge));
                if (!$hasEdge) {
                    $this->rootVertex->shouldReceive('createEdgeTo')->once()->with($vertex)->andReturn($edge);
                }
            }
        } else {
            $this->rootVertex = $vertex;
        }

        return $name;
    }

    public function testDependencyCreationInCallMode()
    {
        $this->fixture->setCallMode();
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array());
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->andReturn(false);

        $this->prepareDependencyCreation();

        $declared = $this->createName('Dec\\Name');

        $this->adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declared);

        $this->adt->shouldReceive('getImplementedNamespaces')->never();
        $this->adt->shouldReceive('getExtendedNamespaces')->never();
        $this->adt->shouldReceive('getUsedTraitNamespaces')->never();
        $this->adt->shouldReceive('getUsedNamespaces')->never();

        $called1 = $this->createName('Called\\Name1', $declared);
        $called2 = $this->createName('Called\\Name2', $declared);

        $uns1 = $this->createName('Uns\\Name1', $declared, false);
        $uns2 = $this->createName('Uns\\Name2', $declared, false);

        $string1 = $this->createName('String\\Name1', $declared, true);
        $string2 = $this->createName('String\\Name2', $declared, false);

        $this->adt->shouldReceive('getCalledNamespaces')->once()->andReturn(array($called1, $called2));
        $this->adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array($uns1, $uns2));
        $this->adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array($string1, $string2));

        self::assertSame($this->fixture, $this->fixture->create());
    }

    public function testDependencyCreationNotInCallMode()
    {
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array());
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->andReturn(false);
        $this->prepareDependencyCreation();

        $declared = $this->createName('Dec\\Name');

        $this->adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declared);

        $this->adt->shouldReceive('getCalledNamespaces')->never();

        $imp1 = $this->createName('Imp\\Name1', $declared, false);
        $imp2 = $this->createName('Imp\\Name2', $declared, false);

        $ext = $this->createName('Ext\\Name1', $declared, false);

        $trait1 = $this->createName('Trait\\Name1', $declared, false);
        $trait2 = $this->createName('Trait\\Name2', $declared, false);

        $used1 = $this->createName('Used\\Name1', $declared);
        $used2 = $this->createName('Used\\Name2', $declared);
        $used3 = $this->createName('Used\\Name2', $declared, true);
        $used4 = $this->createName('Used\\Name2', $declared);

        $uns1 = $this->createName('Uns\\Name1', $declared, true);
        $uns2 = $this->createName('Uns\\Name2', $declared, false);

        $string1 = $this->createName('String\\Name1', $declared, false);
        $string2 = $this->createName('String\\Name2', $declared, false);

        $meta = \Mockery::mock('PhpDa\Entity\Meta');
        $this->adt->shouldReceive('getMeta')->andReturn($meta);

        $meta->shouldReceive('getImplementedNamespaces')->once()->andReturn(array($imp1, $imp2));
        $meta->shouldReceive('getExtendedNamespaces')->once()->andReturn(array($ext));
        $meta->shouldReceive('getUsedTraitNamespaces')->once()->andReturn(array($trait1, $trait2));
        $this->adt->shouldReceive('getUsedNamespaces')->once()->andReturn(array($used1, $used2, $used3, $used4));
        $this->adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array($uns1, $uns2));
        $this->adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array($string1, $string2));

        self::assertSame($this->fixture, $this->fixture->create());
    }

    public function testDependencyCreationWhenAdtIsGlobalNamespace()
    {
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array());
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->andReturn(true);
        $this->prepareDependencyCreation();

        $this->adt->shouldReceive('getDeclaredNamespace')->never();
        $this->adt->shouldReceive('getCalledNamespaces')->never();
        $this->adt->shouldReceive('getUsedNamespaces')->never();
        $this->adt->shouldReceive('getUnsupportedStmts')->never();
        $this->adt->shouldReceive('getNamespacedStrings')->never();

        self::assertSame($this->fixture, $this->fixture->create());
    }

    public function testDependencyCreationWithReferenceValidator()
    {
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array());
        $validator = \Mockery::mock('PhpDA\Reference\ValidatorInterface');
        $this->fixture->setReferenceValidator($validator);
        $this->fixture->setCallMode();
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->andReturn(false);

        $this->prepareDependencyCreation();

        $declared = $this->createName('Dec\\Name');

        $this->adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declared);

        $called1 = $this->createName('Called\\Name1', $declared);

        $this->adt->shouldReceive('getCalledNamespaces')->once()->andReturn(array($called1));
        $this->adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array());
        $this->adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array());

        $validator->shouldReceive('isValidBetween')->andReturnUsing(
            function ($from, $to) use ($declared, $called1) {
                self::assertNotSame($from, $declared);
                self::assertEquals($from, $declared);
                self::assertNotSame($to, $called1);
                self::assertEquals($to, $called1);
                return true;
            }
        );

        self::assertSame($this->fixture, $this->fixture->create());
        self::assertFalse($this->fixture->hasViolations());
    }

    public function testDependencyCreationWithReferenceValidatorAndInvalidEdge()
    {
        $validator = \Mockery::mock('PhpDA\Reference\ValidatorInterface');
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array());
        $this->fixture->setReferenceValidator($validator);
        $this->fixture->setCallMode();
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->andReturn(false);

        $this->prepareDependencyCreation();

        $validatorMessages = array(1, 2);
        $declared = $this->createName('Dec\\Name');

        $this->adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declared);

        $called1 = $this->createName('Called\\Name1', $declared);

        $this->adt->shouldReceive('getCalledNamespaces')->once()->andReturn(array($called1));
        $this->adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array());
        $this->adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array());

        $validator->shouldReceive('isValidBetween')->andReturn(false);
        $validator->shouldReceive('getMessages')->andReturn($validatorMessages);

        self::assertSame($this->fixture, $this->fixture->create());
        self::assertTrue($this->fixture->hasViolations());
    }

    public function testDependencyCreationWithDetectedCyleAndInvalidEdge()
    {
        $validator = \Mockery::mock('PhpDA\Reference\ValidatorInterface');
        $edge = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edge->shouldReceive('setAttribute');
        $edge->shouldReceive('getAttribute')->with('referenceValidatorMessages')->andReturn(null);
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array($edge));
        $this->fixture->setReferenceValidator($validator);
        $this->fixture->setCallMode();
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->andReturn(false);

        $this->prepareDependencyCreation();

        $validatorMessages = array(1, 2);
        $declared = $this->createName('Dec\\Name');

        $this->adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declared);

        $called1 = $this->createName('Called\\Name1', $declared);

        $this->adt->shouldReceive('getCalledNamespaces')->once()->andReturn(array($called1));
        $this->adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array());
        $this->adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array());

        $validator->shouldReceive('isValidBetween')->andReturn(false);
        $validator->shouldReceive('getMessages')->andReturn($validatorMessages);

        self::assertSame($this->fixture, $this->fixture->create());
        self::assertTrue($this->fixture->hasViolations());
    }

    public function testDependencyCreationWithDetectedCyle()
    {
        $validator = \Mockery::mock('PhpDA\Reference\ValidatorInterface');
        $edge = \Mockery::mock('Fhaculty\Graph\Edge\Directed');
        $edge->shouldReceive('setAttribute');
        $edge->shouldReceive('getAttribute')->with('referenceValidatorMessages')->andReturn(true);
        $this->cycleDetector->shouldReceive('getCycledEdges')->andReturn(array($edge));
        $this->fixture->setReferenceValidator($validator);
        $this->fixture->setCallMode();
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->andReturn(false);

        $this->prepareDependencyCreation();

        $validatorMessages = array(1, 2);
        $declared = $this->createName('Dec\\Name');

        $this->adt->shouldReceive('getDeclaredNamespace')->once()->andReturn($declared);

        $called1 = $this->createName('Called\\Name1', $declared);

        $this->adt->shouldReceive('getCalledNamespaces')->once()->andReturn(array($called1));
        $this->adt->shouldReceive('getUnsupportedStmts')->once()->andReturn(array());
        $this->adt->shouldReceive('getNamespacedStrings')->once()->andReturn(array());

        $validator->shouldReceive('isValidBetween')->andReturn(false);
        $validator->shouldReceive('getMessages')->andReturn($validatorMessages);

        self::assertSame($this->fixture, $this->fixture->create());
        self::assertTrue($this->fixture->hasViolations());
    }
}
