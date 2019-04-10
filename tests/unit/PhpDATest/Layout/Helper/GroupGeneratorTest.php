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

namespace PhpDATest\Layout\Helper;

use PhpDA\Layout\Helper\GroupGenerator;

class GroupGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var GroupGenerator */
    protected $fixture;

    /** @var \PhpParser\Node\Name | \Mockery\MockInterface */
    protected $namespace;

    protected function setUp()
    {
        $this->namespace = \Mockery::mock('PhpParser\Node\Name');
        $this->namespace->parts = array('Foo', 'Bar', 'Baz');

        $this->fixture = new GroupGenerator;
    }

    public function testIdRetrievalWithEmptyGroupLength()
    {
        self::assertSame(array(), $this->fixture->getGroups());
        self::assertNull($this->fixture->getIdFor($this->namespace));
        self::assertSame(array(), $this->fixture->getGroups());
    }

    public function testIdRetrievalWithGroupLength()
    {
        $this->fixture->setGroupLength(2);
        self::assertSame(array(), $this->fixture->getGroups());

        self::assertSame(-1, $this->fixture->getIdFor($this->namespace));
        self::assertSame(array(-1 => 'Foo\\Bar'), $this->fixture->getGroups());

        self::assertSame(-1, $this->fixture->getIdFor($this->namespace));
        self::assertSame(array(-1 => 'Foo\\Bar'), $this->fixture->getGroups());

        $this->namespace->parts = array('Baz', 'Foo');

        self::assertSame(-2, $this->fixture->getIdFor($this->namespace));
        self::assertSame(array(-1 => 'Foo\\Bar', -2 => 'Baz\\Foo'), $this->fixture->getGroups());

        self::assertSame(-2, $this->fixture->getIdFor($this->namespace));
        self::assertSame(array(-1 => 'Foo\\Bar', -2 => 'Baz\\Foo'), $this->fixture->getGroups());

        $this->namespace->parts = array('Baz');

        self::assertSame(-3, $this->fixture->getIdFor($this->namespace));
        self::assertSame(array(-1 => 'Foo\\Bar', -2 => 'Baz\\Foo', -3 => 'Baz'), $this->fixture->getGroups());

        self::assertSame(-3, $this->fixture->getIdFor($this->namespace));
        self::assertSame(array(-1 => 'Foo\\Bar', -2 => 'Baz\\Foo', -3 => 'Baz'), $this->fixture->getGroups());
    }
}
