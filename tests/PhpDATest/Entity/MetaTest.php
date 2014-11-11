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

use PhpDA\Entity\Meta;

class MetaTest extends \PHPUnit_Framework_TestCase
{
    /** @var Meta */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Meta;
    }

    public function testIsClass()
    {
        $this->assertFalse($this->fixture->isClass());
        $this->assertFalse($this->fixture->isTrait());
        $this->assertFalse($this->fixture->isInterface());

        $this->fixture->setClass();
        $this->assertTrue($this->fixture->isClass());
        $this->assertFalse($this->fixture->isTrait());
        $this->assertFalse($this->fixture->isInterface());
    }

    public function testIsInterface()
    {
        $this->assertFalse($this->fixture->isClass());
        $this->assertFalse($this->fixture->isTrait());
        $this->assertFalse($this->fixture->isInterface());

        $this->fixture->setInterface();
        $this->assertFalse($this->fixture->isClass());
        $this->assertFalse($this->fixture->isTrait());
        $this->assertTrue($this->fixture->isInterface());
    }

    public function testIsTrait()
    {
        $this->assertFalse($this->fixture->isClass());
        $this->assertFalse($this->fixture->isTrait());
        $this->assertFalse($this->fixture->isInterface());

        $this->fixture->setTrait();
        $this->assertFalse($this->fixture->isClass());
        $this->assertTrue($this->fixture->isTrait());
        $this->assertFalse($this->fixture->isInterface());
    }

    public function testIsAbstract()
    {
        $this->assertFalse($this->fixture->isAbstract());
        $this->fixture->setAbstract();
        $this->assertTrue($this->fixture->isAbstract());
    }

    public function testIsFinal()
    {
        $this->assertFalse($this->fixture->isFinal());
        $this->fixture->setFinal();
        $this->assertTrue($this->fixture->isFinal());
    }

    public function testMutateAndAccessExtendedNamespaces()
    {
        $this->assertSame(array(), $this->fixture->getExtendedNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addExtendedNamespace($name1);
        $this->fixture->addExtendedNamespace($name2);

        $this->assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getExtendedNamespaces());
    }

    public function testMutateAndAccessImplementedNamespaces()
    {
        $this->assertSame(array(), $this->fixture->getImplementedNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addImplementedNamespace($name1);
        $this->fixture->addImplementedNamespace($name2);

        $this->assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getImplementedNamespaces());
    }

    public function testMutateAndAccessUsedTraitNamespaces()
    {
        $this->assertSame(array(), $this->fixture->getUsedTraitNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addUsedTraitNamespace($name1);
        $this->fixture->addUsedTraitNamespace($name2);

        $this->assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getUsedTraitNamespaces());
    }

    public function testMutateAndAccessAllNamespaces()
    {
        $this->assertSame(array(), $this->fixture->getAllNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $name3 = \Mockery::mock('PhpParser\Node\Name');
        $name3->shouldReceive('toString')->andReturn('3');
        $this->fixture->addImplementedNamespace($name1);
        $this->fixture->addUsedTraitNamespace($name2);
        $this->fixture->addExtendedNamespace($name3);

        $namespaces = $this->fixture->getAllNamespaces();

        $this->assertSame(3, sizeof($namespaces));
        $this->assertSame($name1, $namespaces['1']);
        $this->assertSame($name2, $namespaces['2']);
        $this->assertSame($name3, $namespaces['3']);
    }
}
