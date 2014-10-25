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

use PhpDA\Parser\Visitor\SuperglobalCollector;

class SuperglobalCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var SuperglobalCollector */
    protected $fixture;

    /** @var \PhpDA\Entity\Adt | \Mockery\MockInterface */
    protected $adt;

    /** @var \PhpDA\Parser\Filter\NodeNameInterface | \Mockery\MockInterface */
    protected $nodeNameFilter;

    protected function setUp()
    {
        $this->adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->nodeNameFilter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');

        $this->fixture = new SuperglobalCollector;
        $this->fixture->setAdt($this->adt);
        $this->fixture->setNodeNameFilter($this->nodeNameFilter);
    }

    public function testNotCollectingByInvalidNode()
    {
        $node = \Mockery::mock('PhpParser\Node');
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingByNotMatchingVar()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\Variable');
        $node->name = 'foo';
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingGlobalsByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('GLOBALS');
    }

    public function testNotCollectingServerByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_SERVER');
    }

    public function testNotCollectingQueryByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_GET');
    }

    public function testNotCollectingPostByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_POST');
    }

    public function testNotCollectingFilesByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_FILES');
    }

    public function testNotCollectingCookieByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_COOKIE');
    }

    public function testNotCollectingSessionByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_SESSION');
    }

    public function testNotCollectingRequestByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_REQUEST');
    }

    public function testNotCollectingEnvByFilteredToNull()
    {
        $this->assertNotCollectingByFilteredToNull('_ENV');
    }

    protected function assertNotCollectingByFilteredToNull($var)
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\Variable');
        $node->name = $var;
        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testCollectionGlobals()
    {
        $this->assertCollecting('GLOBALS');
    }

    public function testCollectionServer()
    {
        $this->assertCollecting('_SERVER');
    }

    public function testCollectionQuery()
    {
        $this->assertCollecting('_GET');
    }

    public function testCollectionPost()
    {
        $this->assertCollecting('_POST');
    }

    public function testCollectionFiles()
    {
        $this->assertCollecting('_FILES');
    }

    public function testCollectionCookie()
    {
        $this->assertCollecting('_COOKIE');
    }

    public function testCollectionSession()
    {
        $this->assertCollecting('_SESSION');
    }

    public function testCollectionRequest()
    {
        $this->assertCollecting('_REQUEST');
    }

    public function testCollectionEnv()
    {
        $this->assertCollecting('_ENV');
    }

    protected function assertCollecting($var)
    {
        $testcase = $this;
        $attributes = array('foo' => 'bar');
        $node = \Mockery::mock('PhpParser\Node\Expr\Variable');
        $node->shouldReceive('getAttributes')->once()->andReturn($attributes);
        $node->name = $var;
        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturnUsing(
            function ($object) {
                return $object;
            }
        );
        $this->adt->shouldReceive('addUsedNamespace')->once()->andReturnUsing(
            function ($object) use ($testcase, $var, $attributes) {
                /** @var \PhpParser\Node\Name $object */
                $testcase->assertInstanceOf('PhpParser\Node\Name', $object);
                $testcase->assertSame($object->toString(), $var);
                $testcase->assertSame($object->getAttributes(), $attributes);
            }
        );

        $this->fixture->leaveNode($node);
    }
}
