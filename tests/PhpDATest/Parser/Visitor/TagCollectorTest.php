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

namespace PhpDATest\Parser\Visitor;

use PhpDA\Parser\Visitor\Required\NameResolver;
use PhpDA\Parser\Visitor\TagCollector;

class TagCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var TagCollector */
    protected $fixture;

    /** @var \PhpDA\Entity\Adt | \Mockery\MockInterface */
    protected $adt;

    /** @var \PhpDA\Parser\Filter\NodeNameInterface | \Mockery\MockInterface */
    protected $nodeNameFilter;

    protected function setUp()
    {
        try {
            $this->adt = \Mockery::mock('PhpDA\Entity\Adt');
            $this->nodeNameFilter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');
            $this->fixture = new TagCollector;
            $this->fixture->setAdt($this->adt);
            $this->fixture->setNodeNameFilter($this->nodeNameFilter);
        } catch (\LogicException $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    public function testNotCollectingNodeNotHavingTagNames()
    {
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('hasAttribute')->once()->with(NameResolver::TAG_NAMES_ATTRIBUTE)->andReturn(false);
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingNodeHavingEmptyTagNames()
    {
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('hasAttribute')->once()->with(NameResolver::TAG_NAMES_ATTRIBUTE)->andReturn(true);
        $node->shouldReceive('getAttribute')->once()->with(NameResolver::TAG_NAMES_ATTRIBUTE)->andReturn(array());
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingByFilteredToNull()
    {
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('hasAttribute')->once()
            ->with(NameResolver::TAG_NAMES_ATTRIBUTE)->andReturn(true);
        $node->shouldReceive('getAttribute')->once()
            ->with(NameResolver::TAG_NAMES_ATTRIBUTE)->andReturn(array('foo', 'bar'));
        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testCollecting()
    {
        $testcase = $this;
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('hasAttribute')->once()
            ->with(NameResolver::TAG_NAMES_ATTRIBUTE)->andReturn(true);
        $node->shouldReceive('getAttribute')->once()
            ->with(NameResolver::TAG_NAMES_ATTRIBUTE)->andReturn(array('foo', 'bar'));
        $this->nodeNameFilter->shouldReceive('filter')->twice()->andReturnUsing(
            function ($object) {
                return $object;
            }
        );
        $this->adt->shouldReceive('addUsedNamespace')->twice()->andReturnUsing(
            function ($object) use ($testcase) {
                /** @var \PhpParser\Node\Name $object */
                $testcase->assertInstanceOf('PhpParser\Node\Name', $object);
                $testcase->assertContains($object->toString(), array('foo', 'bar'));
            }
        );

        $this->fixture->leaveNode($node);
    }
}
