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
        self::assertFalse($this->fixture->isClass());
        self::assertFalse($this->fixture->isTrait());
        self::assertFalse($this->fixture->isInterface());

        $this->fixture->setClass();
        self::assertTrue($this->fixture->isClass());
        self::assertFalse($this->fixture->isTrait());
        self::assertFalse($this->fixture->isInterface());
    }

    public function testIsInterface()
    {
        self::assertFalse($this->fixture->isClass());
        self::assertFalse($this->fixture->isTrait());
        self::assertFalse($this->fixture->isInterface());

        $this->fixture->setInterface();
        self::assertFalse($this->fixture->isClass());
        self::assertFalse($this->fixture->isTrait());
        self::assertTrue($this->fixture->isInterface());
    }

    public function testIsTrait()
    {
        self::assertFalse($this->fixture->isClass());
        self::assertFalse($this->fixture->isTrait());
        self::assertFalse($this->fixture->isInterface());

        $this->fixture->setTrait();
        self::assertFalse($this->fixture->isClass());
        self::assertTrue($this->fixture->isTrait());
        self::assertFalse($this->fixture->isInterface());
    }

    public function testIsAbstract()
    {
        self::assertFalse($this->fixture->isAbstract());
        $this->fixture->setAbstract();
        self::assertTrue($this->fixture->isAbstract());
    }

    public function testIsFinal()
    {
        self::assertFalse($this->fixture->isFinal());
        $this->fixture->setFinal();
        self::assertTrue($this->fixture->isFinal());
    }

    public function testMutateAndAccessExtendedNamespaces()
    {
        self::assertSame(array(), $this->fixture->getExtendedNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addExtendedNamespace($name1);
        $this->fixture->addExtendedNamespace($name2);

        self::assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getExtendedNamespaces());
    }

    public function testMutateAndAccessImplementedNamespaces()
    {
        self::assertSame(array(), $this->fixture->getImplementedNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addImplementedNamespace($name1);
        $this->fixture->addImplementedNamespace($name2);

        self::assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getImplementedNamespaces());
    }

    public function testMutateAndAccessUsedTraitNamespaces()
    {
        self::assertSame(array(), $this->fixture->getUsedTraitNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addUsedTraitNamespace($name1);
        $this->fixture->addUsedTraitNamespace($name2);

        self::assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getUsedTraitNamespaces());
    }

    public function testMutateAndAccessAllNamespaces()
    {
        self::assertSame(array(), $this->fixture->getAllNamespaces());

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

        self::assertSame(3, sizeof($namespaces));
        self::assertSame($name1, $namespaces['1']);
        self::assertSame($name2, $namespaces['2']);
        self::assertSame($name3, $namespaces['3']);
    }

    public function testArrayRepresentation()
    {
        self::assertSame(
            array(
                'type'                  => '',
                'implementedNamespaces' => array(),
                'extendedNamespaces'    => array(),
                'usedTraitNamespaces'   => array(),
                'isAbstract'            => false,
                'isFinal'               => false,
            ),
            $this->fixture->toArray()
        );
    }
}
