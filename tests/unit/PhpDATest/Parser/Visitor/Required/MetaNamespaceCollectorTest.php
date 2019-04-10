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

namespace PhpDATest\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\Required\MetaNamespaceCollector;

class MetaNamespaceCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MetaNamespaceCollector */
    protected $fixture;

    /** @var \PhpDA\Entity\Adt | \Mockery\MockInterface */
    protected $adt;

    /** @var \PhpDA\Entity\Meta | \Mockery\MockInterface */
    protected $meta;

    /** @var \PhpDA\Parser\Filter\NodeNameInterface | \Mockery\MockInterface */
    protected $nodeNameFilter;

    /** @var array */
    protected $attributes = array('foo' => 'bar');

    protected function setUp()
    {
        $this->meta = \Mockery::mock('PhpDA\Entity\Meta');
        $this->adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->adt->shouldReceive('getMeta')->andReturn($this->meta);
        $this->nodeNameFilter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');

        $this->fixture = new MetaNamespaceCollector;
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

        $node->shouldReceive('isAbstract')->once()->andReturn(true);
        $node->shouldReceive('isFinal')->once()->andReturn(true);
        $this->meta->shouldReceive('setAbstract')->once();
        $this->meta->shouldReceive('setFinal')->once();
        $this->meta->shouldReceive('setClass')->once();
        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingInterfaceByFilteredToNull()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $node->extends = array(
            \Mockery::mock('PhpParser\Node\Name'),
            \Mockery::mock('PhpParser\Node\Name'),
        );

        $this->meta->shouldReceive('setInterface')->once();
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
        $node->shouldReceive('isAbstract')->once()->andReturn(false);
        $node->shouldReceive('isFinal')->once()->andReturn(false);

        $implements = \Mockery::mock('PhpParser\Node\Name');
        $implements->shouldReceive('getAttributes')->once()->andReturn($this->attributes);
        $implements->shouldReceive('setAttribute')->once()->with('foo', 'bar');
        $extends = \Mockery::mock('PhpParser\Node\Name');
        $extends->shouldReceive('getAttributes')->once()->andReturn($this->attributes);
        $extends->shouldReceive('setAttribute')->once()->with('foo', 'bar');
        $node->implements = array($implements);
        $node->extends = $extends;

        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturnUsing(
            function ($object) {
                return $object;
            }
        );
        $this->meta->shouldReceive('setClass')->once();
        $this->meta->shouldReceive('addImplementedNamespace')->once()->with($implements);
        $this->meta->shouldReceive('addExtendedNamespace')->once()->with($extends);

        $this->fixture->leaveNode($node);
    }

    public function testCollectingClassWithoutExtendsAndImplements()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $node->shouldReceive('isAbstract')->once()->andReturn(false);
        $node->shouldReceive('isFinal')->once()->andReturn(false);

        $node->implements = array();
        $node->extends = null;

        $this->meta->shouldReceive('setClass')->once();

        $this->fixture->leaveNode($node);
    }

    public function testCollectingTrait()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $this->meta->shouldReceive('setTrait')->once();

        $this->fixture->leaveNode($node);
    }

    public function testCollectingInterface()
    {
        $name = \Mockery::mock('PhpParser\Node\Name');
        $name->shouldReceive('getAttributes')->once()->andReturn($this->attributes);
        $name->shouldReceive('setAttribute')->once()->with('foo', 'bar');
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $node->extends = array($name);

        $this->nodeNameFilter->shouldReceive('filter')->once()->with($name)->andReturn($name);

        $this->meta->shouldReceive('setInterface')->once();
        $this->meta->shouldReceive('addExtendedNamespace')->once()->with($name);

        $this->fixture->leaveNode($node);
    }

    public function testCollectingInterfaceWithoutExtends()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $node->extends = array();

        $this->meta->shouldReceive('setInterface')->once();

        $this->fixture->leaveNode($node);
    }

    public function testCollectingTraitUse()
    {
        $name = \Mockery::mock('PhpParser\Node\Name');
        $name->shouldReceive('getAttributes')->once()->andReturn($this->attributes);
        $name->shouldReceive('setAttribute')->once()->with('foo', 'bar');
        $node = \Mockery::mock('PhpParser\Node\Stmt\TraitUse');
        $node->traits = array($name);

        $this->nodeNameFilter->shouldReceive('filter')->once()->with($name)->andReturn($name);
        $this->meta->shouldReceive('addUsedTraitNamespace')->once()->with($name);

        $this->fixture->leaveNode($node);
    }
}
