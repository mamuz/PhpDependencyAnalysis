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

use PhpDA\Entity\Adt;
use PhpParser\Node\Name;

class AdtTest extends \PHPUnit_Framework_TestCase
{
    /** @var Adt */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Adt;
    }

    public function testMetaAccess()
    {
        self::assertInstanceOf('PhpDA\Entity\Meta', $this->fixture->getMeta());
    }

    public function testMutateAndAccessDeclaredNamespace()
    {
        $name = $this->fixture->getDeclaredNamespace();
        self::assertInstanceOf('PhpParser\Node\Name', $name);
        self::assertSame('\\', $name->toString());
        self::assertTrue($this->fixture->hasDeclaredGlobalNamespace());

        $name = \Mockery::mock('PhpParser\Node\Name');
        $name->shouldReceive('toString')->andReturn('Foo');
        $this->fixture->setDeclaredNamespace($name);

        self::assertSame($name, $this->fixture->getDeclaredNamespace());
        self::assertFalse($this->fixture->hasDeclaredGlobalNamespace());
    }

    public function testMutateAndAccessCalledNamespace()
    {
        self::assertSame(array(), $this->fixture->getCalledNamespaces());

        $declaredNamespace = \Mockery::mock('PhpParser\Node\Name');
        $declaredNamespace->shouldReceive('toString')->andReturn('Foo\Declare');
        $this->fixture->setDeclaredNamespace($declaredNamespace);

        $implementNamespace = \Mockery::mock('PhpParser\Node\Name');
        $implementNamespace->shouldReceive('toString')->andReturn('Foo\Implement');
        $this->fixture->getMeta()->addImplementedNamespace($implementNamespace);

        $extendNamespace = \Mockery::mock('PhpParser\Node\Name');
        $extendNamespace->shouldReceive('toString')->andReturn('Foo\Extend');
        $this->fixture->getMeta()->addExtendedNamespace($extendNamespace);

        $traitUseNamespace = \Mockery::mock('PhpParser\Node\Name');
        $traitUseNamespace->shouldReceive('toString')->andReturn('Foo\TraitUse');
        $this->fixture->getMeta()->addUsedTraitNamespace($traitUseNamespace);

        $unsupportedNamespace = \Mockery::mock('PhpParser\Node\Name');
        $unsupportedNamespace->shouldReceive('toString')->andReturn('Foo\Unsupported');
        $this->fixture->addUnsupportedStmt($unsupportedNamespace);

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addUsedNamespace($name1);
        $this->fixture->addUsedNamespace($name2);
        $this->fixture->addUsedNamespace($declaredNamespace);
        $this->fixture->addUsedNamespace($implementNamespace);
        $this->fixture->addUsedNamespace($extendNamespace);
        $this->fixture->addUsedNamespace($traitUseNamespace);
        $this->fixture->addUsedNamespace($unsupportedNamespace);

        self::assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getCalledNamespaces());
    }

    public function testMutateAndAccessUsedNamespaces()
    {
        self::assertSame(array(), $this->fixture->getUsedNamespaces());

        $unsupportedNamespace = \Mockery::mock('PhpParser\Node\Name');
        $unsupportedNamespace->shouldReceive('toString')->andReturn('Foo\Unsupported');
        $this->fixture->addUnsupportedStmt($unsupportedNamespace);

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addUsedNamespace($name1);
        $this->fixture->addUsedNamespace($name2);
        $this->fixture->addUsedNamespace($unsupportedNamespace);

        self::assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getUsedNamespaces());
    }

    public function testMutateAndAccessUnsupportedStmts()
    {
        self::assertSame(array(), $this->fixture->getUnsupportedStmts());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addUnsupportedStmt($name1);
        $this->fixture->addUnsupportedStmt($name2);

        self::assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getUnsupportedStmts());
    }

    public function testMutateAndAccessNamespacedStrings()
    {
        self::assertSame(array(), $this->fixture->getNamespacedStrings());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->andReturn('1');
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn('2');
        $this->fixture->addNamespacedString($name1);
        $this->fixture->addNamespacedString($name2);

        self::assertSame(array('1' => $name1, '2' => $name2), $this->fixture->getNamespacedStrings());
    }

    public function testArrayRepresentation()
    {
        $this->fixture->getMeta()->addImplementedNamespace(new Name('Foo\implementedNamespaces'));
        $this->fixture->getMeta()->addExtendedNamespace(new Name('Foo\extendedNamespaces'));
        $this->fixture->getMeta()->addUsedTraitNamespace(new Name('Foo\usedTraitNamespaces'));

        $meta = array();
        $meta['type'] = '';
        $meta['implementedNamespaces'] = array('Foo\implementedNamespaces' => 'Foo\implementedNamespaces');
        $meta['extendedNamespaces'] = array('Foo\extendedNamespaces' => 'Foo\extendedNamespaces');
        $meta['usedTraitNamespaces'] = array('Foo\usedTraitNamespaces' => 'Foo\usedTraitNamespaces');
        $meta['isAbstract'] = false;
        $meta['isFinal'] = false;

        $this->fixture->addUsedNamespace(new Name('Foo\usedNamespaces'));
        $this->fixture->addUnsupportedStmt(new Name('Foo\unsupportedStmts'));
        $this->fixture->addNamespacedString(new Name('Foo\namespacedStrings'));

        self::assertSame(
            array(
                'meta'              => $meta,
                'usedNamespaces'    => array('Foo\usedNamespaces' => 'Foo\usedNamespaces'),
                'unsupportedStmts'  => array('Foo\unsupportedStmts' => 'Foo\unsupportedStmts'),
                'namespacedStrings' => array('Foo\namespacedStrings' => 'Foo\namespacedStrings'),
            ),
            $this->fixture->toArray()
        );
    }
}
