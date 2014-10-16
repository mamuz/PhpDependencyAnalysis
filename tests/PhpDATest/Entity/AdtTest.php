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

use PhpDA\Entity\Adt;

class AdtTest extends \PHPUnit_Framework_TestCase
{
    /** @var Adt */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Adt;
    }

    public function testMutateAndAccessDeclaredNamespace()
    {
        $name = $this->fixture->getDeclaredNamespace();
        $this->assertInstanceOf('PhpParser\Node\Name', $name);
        $this->assertSame('\\', $name->toString());

        $name = \Mockery::mock('PhpParser\Node\Name');
        $this->fixture->setDeclaredNamespace($name);

        $this->assertSame($name, $this->fixture->getDeclaredNamespace());
    }

    public function testMutateAndAccessUsedNamespace()
    {
        $this->assertSame(array(), $this->fixture->getUsedNamespaces());

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->once();
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->once();
        $this->fixture->addUsedNamespace($name1);
        $this->fixture->addUsedNamespace($name2);

        $this->assertSame(array($name1, $name2), $this->fixture->getUsedNamespaces());
    }

    public function testMutateAndAccessUsedNamespaceWithFilteredDeclaredNamespace()
    {
        $declaredNamespace = 'Foo\Bar';

        $name = \Mockery::mock('PhpParser\Node\Name');
        $name->shouldReceive('toString')->andReturn($declaredNamespace);
        $this->fixture->setDeclaredNamespace($name);

        $name1 = \Mockery::mock('PhpParser\Node\Name');
        $name1->shouldReceive('toString')->once();
        $name2 = \Mockery::mock('PhpParser\Node\Name');
        $name2->shouldReceive('toString')->andReturn($declaredNamespace);
        $name3 = \Mockery::mock('PhpParser\Node\Name');
        $name3->shouldReceive('toString')->once();

        $this->fixture->addUsedNamespace($name1);
        $this->fixture->addUsedNamespace($name2);
        $this->fixture->addUsedNamespace($name3);

        $this->assertSame(array($name1, $name3), $this->fixture->getUsedNamespaces());
    }
}
