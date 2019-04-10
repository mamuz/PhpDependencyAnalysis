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
        self::assertNotCollectingByFilteredToNull('GLOBALS');
    }

    public function testNotCollectingServerByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_SERVER');
    }

    public function testNotCollectingQueryByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_GET');
    }

    public function testNotCollectingPostByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_POST');
    }

    public function testNotCollectingFilesByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_FILES');
    }

    public function testNotCollectingCookieByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_COOKIE');
    }

    public function testNotCollectingSessionByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_SESSION');
    }

    public function testNotCollectingRequestByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_REQUEST');
    }

    public function testNotCollectingEnvByFilteredToNull()
    {
        self::assertNotCollectingByFilteredToNull('_ENV');
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
        self::assertCollecting('GLOBALS');
    }

    public function testCollectionServer()
    {
        self::assertCollecting('_SERVER');
    }

    public function testCollectionQuery()
    {
        self::assertCollecting('_GET');
    }

    public function testCollectionPost()
    {
        self::assertCollecting('_POST');
    }

    public function testCollectionFiles()
    {
        self::assertCollecting('_FILES');
    }

    public function testCollectionCookie()
    {
        self::assertCollecting('_COOKIE');
    }

    public function testCollectionSession()
    {
        self::assertCollecting('_SESSION');
    }

    public function testCollectionRequest()
    {
        self::assertCollecting('_REQUEST');
    }

    public function testCollectionEnv()
    {
        self::assertCollecting('_ENV');
    }

    protected function assertCollecting($var)
    {
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
            function ($object) use ($var) {
                /** @var \PhpParser\Node\Name $object */
                self::assertInstanceOf('PhpParser\Node\Name', $object);
                self::assertSame($object->toString(), $var);
            }
        );

        $this->fixture->leaveNode($node);
    }
}
