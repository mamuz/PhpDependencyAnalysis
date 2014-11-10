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

namespace PhpDATest\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\Required\InheritNamespaceCollector;

class InheritNamespaceCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var InheritNamespaceCollector */
    protected $fixture;

    /** @var \PhpDA\Entity\Adt | \Mockery\MockInterface */
    protected $adt;

    /** @var \PhpDA\Parser\Filter\NodeNameInterface | \Mockery\MockInterface */
    protected $nodeNameFilter;

    protected function setUp()
    {
        $this->adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->nodeNameFilter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');

        $this->fixture = new InheritNamespaceCollector;
        $this->fixture->setAdt($this->adt);
        $this->fixture->setNodeNameFilter($this->nodeNameFilter);
    }

    public function testNotCollectingByInvalidNode()
    {
        $node = \Mockery::mock('PhpParser\Node');
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingClassByFilteredToNull()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $node->implements = array(\Mockery::mock('PhpParser\Node\Name'));
        $node->extends = array(\Mockery::mock('PhpParser\Node\Name'));

        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingTraitByFilteredToNull()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $node->extends = \Mockery::mock('PhpParser\Node\Name');

        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingInterfaceByFilteredToNull()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $node->extends = array(
            \Mockery::mock('PhpParser\Node\Name'),
            \Mockery::mock('PhpParser\Node\Name'),
        );

        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingTraitUseByFilteredToNull()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\TraitUse');
        $node->traits = array(
            \Mockery::mock('PhpParser\Node\Name'),
            \Mockery::mock('PhpParser\Node\Name'),
        );

        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testCollectingClass()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $node->implements = array(\Mockery::mock('PhpParser\Node\Name'));
        $node->extends = array(\Mockery::mock('PhpParser\Node\Name'));

        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturnUsing(
            function ($object) {
                return $object;
            }
        );
        $this->adt->shouldReceive('addUsedNamespace')->twice();

        $this->fixture->leaveNode($node);
    }

    public function testCollectingTrait()
    {
        $name = \Mockery::mock('PhpParser\Node\Name');
        $node = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $node->extends = array($name);

        $this->nodeNameFilter->shouldReceive('filter')->once()->with($name)->andReturn($name);
        $this->adt->shouldReceive('addUsedNamespace')->once()->with($name);

        $this->fixture->leaveNode($node);
    }

    public function testCollectingInterface()
    {
        $name = \Mockery::mock('PhpParser\Node\Name');
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $node->extends = array($name);

        $this->nodeNameFilter->shouldReceive('filter')->once()->with($name)->andReturn($name);
        $this->adt->shouldReceive('addUsedNamespace')->once()->with($name);

        $this->fixture->leaveNode($node);
    }

    public function testCollectingTraitUse()
    {
        $name = \Mockery::mock('PhpParser\Node\Name');
        $node = \Mockery::mock('PhpParser\Node\Stmt\TraitUse');
        $node->traits = array($name);

        $this->nodeNameFilter->shouldReceive('filter')->once()->with($name)->andReturn($name);
        $this->adt->shouldReceive('addUsedNamespace')->once()->with($name);

        $this->fixture->leaveNode($node);
    }
}
