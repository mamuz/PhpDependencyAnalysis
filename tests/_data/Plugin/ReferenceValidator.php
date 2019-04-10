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

namespace MyDomain\Plugin;

use PhpDA\Reference\ValidatorInterface;
use PhpParser\Node\Name;

class ReferenceValidator implements ValidatorInterface
{
    const PACKAGE_VIOLATION = 'Violation of reference between packages';

    const LAYER_VIOLATION = 'Violation of reference between layers';

    /** @var array */
    private $messages = [];

    /** @var array */
    private $packageReferences = [
        'PackageA' => ['Zend', 'Doctrine', 'PackageB'],
        'PackageB' => ['Zend', 'Doctrine', 'PackageA'],
        'PackageC' => ['Zend', 'Doctrine', 'PackageA'],
    ];

    /** @var array */
    private $layerReferences = [
        'Controller' => ['Mvc', 'Http', 'Service', 'Entity'],
        'Service'    => ['Service', 'Mapper', 'Entity'],
        'Mapper'     => ['ORM', 'Entity'],
    ];

    public function isValidBetween(Name $from, Name $to)
    {
        $this->messages = [];

        if ($this->isViolation($from->parts[0], $to->parts[0], $this->packageReferences)) {
            $this->messages[] = self::PACKAGE_VIOLATION;
        }

        if (isset($from->parts[1]) && isset($to->parts[1])
            && $this->isViolation($from->parts[1], $to->parts[1], $this->layerReferences)
        ) {
            $this->messages[] = self::LAYER_VIOLATION;
        }

        return empty($this->messages);
    }

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param string $from
     * @param string $to
     * @param array  $references
     * @return bool
     */
    private function isViolation($from, $to, array $references)
    {
        if ($from == $to || !isset($references[$from])) {
            return false;
        }

        return !in_array($to, $references[$from]);
    }
}
