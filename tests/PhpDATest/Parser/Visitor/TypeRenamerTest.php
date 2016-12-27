<?php
/**
 * The MIT License (MIT)
 *
 * Copyright © 2016 Alexander Ustimenko
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

use PhpDA\Parser\Visitor\TypeRenamer;
use PhpParser\Parser;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node\Stmt\Class_;

class TypeRenamerTest extends \PHPUnit_Framework_TestCase
{

    /** @var TypeRenamer */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new TypeRenamer();
    }

    public function testRenameOccursWithSettings()
    {
        $this->fixture->setOptions(array(
            'rename' => array(
                'Test' => 'Production'
            )
        ));
        $nodes = $this->traverseSelf();
        /** @var Class_ */
        $class = end($nodes[0]->stmts);

        $this->assertContains('Production', $class->name);
    }

    public function testRenameNotOccursWithoutSettings()
    {
        $nodes = $this->traverseSelf();
        /** @var Class_ */
        $class = end($nodes[0]->stmts);
        $this->assertNotContains('Production', $class->name);
    }

    private function traverseSelf()
    {
        $lexer = new Emulative(array(
            'usedAttributes' => array(
                'startLine',
                'endLine',
                'startFilePos',
                'endFilePos'
            )
        ));
        $parser = new Parser($lexer);

        $code = file_get_contents(__FILE__);
        $nodes = $parser->parse($code);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($this->fixture);
        $traverser->traverse($nodes);

        return $nodes;
    }
}
