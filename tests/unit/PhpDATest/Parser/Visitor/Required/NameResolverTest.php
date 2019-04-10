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

use PhpDA\Parser\Visitor\Required\NameResolver;

class NameResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @var NameResolver */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new NameResolver;
    }

    public function testExtendingPhpParserNameResolver()
    {
        self::assertInstanceOf('PhpParser\NodeVisitor\NameResolver', $this->fixture);
    }

    public function testImplementingLoggerAwareInterface()
    {
        self::assertInstanceOf('Psr\Log\LoggerAwareInterface', $this->fixture);
    }

    public function testResolving()
    {
        $node = \Mockery::mock('PhpParser\Node');

        $this->fixture->enterNode($node->shouldIgnoreMissing());
    }

    public function testResolvingDocBlock()
    {
        $docBlock = '
        /**
         * MyClass <please specify short description>
         *
         * @Route("/")
         * @property PhpDaTest\Stub\FooBarProperty
         * @property PhpDaTest\Stub\FooBarProperty2
         * @property PhpDaTest\Stub\FooBarProperty2
         * @method string ignore1(bool $var1, boolean $var2)
         * @method string ignore2(int $var1, integer $var2)
         * @method string ignore3(string $var1, binary $var2)
         * @method string ignore4(object $var1, resource $var2)
         * @method string ignore5(mixed $var1, null $var2)
         * @method string ignore6(float $var1, double $var2)
         * @method string ignore7(this $var1, self $var2)
         * @method string ignore8($this $var1, parent $var2)
         * @method string ignore9(static $var1, true $var2)
         * @method string ignore10(false $var1, TruE $var2)
         * @method string borpu($var)
         * @method int borp() borp(Foo $int1, int | Baz[] $int2, boolean $int4, $int3 = null) any context
         * @method \TimeDate dateFormat(\DateTime $date)
         * @deprecated
         * @property-read MyObject
         * @property-write Foo\Bar
         * @var Collection
         * @Entity
         * @Param string $uppercased
         * @param \Filter\FilterInterface $adapter
         * @param \Faz\Baz[] $collection
         * @param Adapter $adapter
         * @param $adapter
         * @return INTeger|$this
         * @throw DomainException
         * @uses Boo
         * @throws Filter\RuntimeException
         */
        ';

        $comment = \Mockery::mock('PhpParser\Comment\Doc');
        $comment->shouldReceive('getText')->andReturn($docBlock);
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('getDocComment')->once()->andReturn($comment);
        $node->shouldReceive('setAttribute')->once()->with(
            '__tagNames',
            array(
                "PhpDaTest\\Stub\\FooBarProperty"  => "PhpDaTest\\Stub\\FooBarProperty",
                "PhpDaTest\\Stub\\FooBarProperty2" => "PhpDaTest\\Stub\\FooBarProperty2",
                "Foo"                              => "Foo",
                "TimeDate"                         => "TimeDate",
                "DateTime"                         => "DateTime",
                "MyObject"                         => "MyObject",
                "Foo\\Bar"                         => "Foo\\Bar",
                "Collection"                       => "Collection",
                "Filter\\FilterInterface"          => "Filter\\FilterInterface",
                "Faz\\Baz"                         => "Faz\\Baz",
                "Adapter"                          => "Adapter",
                "DomainException"                  => "DomainException",
                "Filter\\RuntimeException"         => "Filter\\RuntimeException",
            )
        );

        $this->fixture->enterNode($node->shouldIgnoreMissing());
    }

    public function testResolvingInlineDocBlock()
    {
        $docBlock = '/** @var Foo\Bar $myVar */';
        $comment = \Mockery::mock('PhpParser\Comment\Doc');
        $comment->shouldReceive('getText')->andReturn($docBlock);
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('getDocComment')->once()->andReturn($comment);
        $node->shouldReceive('setAttribute')->once()->with('__tagNames', array("Foo\\Bar" => "Foo\\Bar"));

        $this->fixture->enterNode($node->shouldIgnoreMissing());
    }

    public function testInvalidDocBlock()
    {
        $file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('warning')->once()->andReturnUsing(
            function ($message, $options) use ($file) {
                self::assertTrue(is_string($message));
                self::assertNotEmpty($message);
                self::assertSame(array($file), $options);
            }
        );

        $this->fixture->setLogger($logger);
        $this->fixture->setFile($file);

        $docBlock = '/** @ A Whitespace after "at" causes PhpDocumentor to throw an exception */';
        $comment = \Mockery::mock('PhpParser\Comment\Doc');
        $comment->shouldReceive('getText')->andReturn($docBlock);
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('getDocComment')->once()->andReturn($comment);

        $this->fixture->enterNode($node->shouldIgnoreMissing());
    }

    public function testInvalidDocBlockWithoutLoggerAndWithoutFile()
    {
        $docBlock = '/** @ A Whitespace after "at" causes PhpDocumentor to throw an exception */';
        $comment = \Mockery::mock('PhpParser\Comment\Doc');
        $comment->shouldReceive('getText')->andReturn($docBlock);
        $node = \Mockery::mock('PhpParser\Node');
        $node->shouldReceive('getDocComment')->once()->andReturn($comment);

        $this->fixture->enterNode($node->shouldIgnoreMissing());
    }
}
