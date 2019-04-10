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

use PhpDA\Entity\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Location */
    protected $fixture;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    /** @var array */
    protected $defaultAttributes = array(
        'startLine' => 30,
        'endLine'   => 40,
        'isComment' => true,
    );

    protected function setUp()
    {
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->file->shouldReceive('__toString')->andReturn('filename');
        $this->fixture = new Location($this->file, $this->createNameMock());
    }

    /**
     * @param array $attributes
     * @return \PhpParser\Node\Name | \Mockery\MockInterface
     */
    protected function createNameMock(array $attributes = null)
    {
        if (is_null($attributes)) {
            $attributes = $this->defaultAttributes;
        }

        $name = \Mockery::mock('PhpParser\Node\Name');
        $name->shouldReceive('getAttributes')->once()->andReturn($attributes);

        return $name;
    }

    public function testAccessFile()
    {
        self::assertSame($this->file, $this->fixture->getFile());
    }

    public function testAccessIsComment()
    {
        self::assertTrue($this->fixture->isComment());

        $attributes = array(
            'startLine' => 30,
            'endLine'   => 40,
        );
        $this->fixture = new Location($this->file, $this->createNameMock($attributes));
        self::assertFalse($this->fixture->isComment());
    }

    public function testAccessStartAndEndLine()
    {
        self::assertSame($this->defaultAttributes['startLine'], $this->fixture->getStartLine());
        self::assertSame($this->defaultAttributes['endLine'], $this->fixture->getEndLine());
    }

    public function testDomainExceptionForMissingStartline()
    {
        self::expectException('DomainException');
        $attributes = array('endLine' => 40);
        $this->fixture = new Location($this->file, $this->createNameMock($attributes));
    }

    public function testDomainExceptionForMissingEndline()
    {
        self::expectException('DomainException');
        $attributes = array('startLine' => 40);
        $this->fixture = new Location($this->file, $this->createNameMock($attributes));
    }

    public function testArrayRepresentation()
    {
        $data = $this->fixture->toArray();

        self::assertArrayHasKey('file', $data);
        self::assertSame(30, $data['startLine']);
        self::assertSame(40, $data['endline']);
        self::assertTrue($data['isComment']);
    }
}
