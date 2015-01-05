<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Marco Muths
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

namespace PhpDA\Entity;

use Symfony\Component\Finder\SplFileInfo;

class Location
{
    /** @var SplFileInfo */
    private $file;

    /** @var int */
    private $startLine;

    /** @var int */
    private $endline;

    /** @var boolean */
    private $isComment = false;

    /** @var string */
    private $fqn;

    /**
     * @param SplFileInfo $file
     * @param array       $attributes
     * @throws \InvalidArgumentException
     */
    public function __construct(SplFileInfo $file, array $attributes)
    {
        if (!isset($attributes['startLine'])
            || !isset($attributes['endLine'])
            || !isset($attributes['fqn'])
        ) {
            throw new \InvalidArgumentException(
                'Values for startLine and/or endLine and/or FQN are not set in attributes'
            );
        }

        $this->file = $file;
        $this->startLine = (int) $attributes['startLine'];
        $this->endline = (int) $attributes['endLine'];
        $this->fqn = (string) $attributes['fqn'];

        if (isset($attributes['isComment'])) {
            $this->isComment = (bool) $attributes['isComment'];
        }
    }

    /**
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return boolean
     */
    public function isComment()
    {
        return $this->isComment;
    }

    /**
     * @return int
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    /**
     * @return int
     */
    public function getEndline()
    {
        return $this->endline;
    }

    /**
     * @return string
     */
    public function getFqn()
    {
        return $this->fqn;
    }
}
