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

use PhpDA\Parser\Visitor\Required\AdtCollector;

class AdtCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var AdtCollector */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new AdtCollector;
    }

    public function testCollecting()
    {
        self::assertSame(array(), $this->fixture->getStmts());

        $invalidNode = \Mockery::mock('PhpParser\Node');
        $classNode = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $traitNode = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $interfaceNode = \Mockery::mock('PhpParser\Node\Stmt\Interface_');

        $this->fixture->leaveNode($invalidNode->shouldIgnoreMissing());
        $this->fixture->leaveNode($classNode->shouldIgnoreMissing());
        $this->fixture->leaveNode($traitNode->shouldIgnoreMissing());
        $this->fixture->leaveNode($interfaceNode->shouldIgnoreMissing());

        self::assertSame(
            array($classNode, $traitNode, $interfaceNode),
            $this->fixture->getStmts()
        );

        $this->fixture->flush();
        self::assertSame(array(), $this->fixture->getStmts());
    }
}
