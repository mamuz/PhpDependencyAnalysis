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

use PhpDA\Layout\Standard;

class StandardTest extends \PHPUnit_Framework_TestCase
{
    /** @var Standard */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Standard('foo');
    }

    public function testAccessGraph()
    {
        $data = $this->fixture->getGraph();
        self::assertNotEmpty($data);
        self::assertSame('foo', $data['label']);
    }

    public function testAccessGroup()
    {
        self::assertNotEmpty($this->fixture->getGroup());
    }

    public function testAccessEdge()
    {
        self::assertNotEmpty($this->fixture->getEdge());
    }

    public function testAccessEdgeExtend()
    {
        self::assertNotEmpty($this->fixture->getEdgeExtend());
    }

    public function testAccessEdgeImplement()
    {
        self::assertNotEmpty($this->fixture->getEdgeImplement());
    }

    public function testAccessEdgeTraitUse()
    {
        self::assertNotEmpty($this->fixture->getEdgeTraitUse());
    }

    public function testAccessEdgeUnsupported()
    {
        self::assertNotEmpty($this->fixture->getEdgeUnsupported());
    }

    public function testAccessEdgeNamespacedString()
    {
        self::assertNotEmpty($this->fixture->getEdgeNamespacedString());
    }

    public function testAccessVertex()
    {
        self::assertNotEmpty($this->fixture->getVertex());
    }

    public function testAccessVertexNamespacedString()
    {
        self::assertNotEmpty($this->fixture->getVertexNamespacedString());
    }

    public function testAccessVertexUnsupported()
    {
        self::assertNotEmpty($this->fixture->getVertexUnsupported());
    }

    public function testAccessEdgeInvalid()
    {
        self::assertNotEmpty($this->fixture->getEdgeInvalid());
    }

    public function testAccessEdgeCycle()
    {
        self::assertNotEmpty($this->fixture->getEdgeCycle());
    }
}
