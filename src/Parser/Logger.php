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

namespace PhpDA\Parser;

use PhpDA\Entity\JsonSerializableFileInfo;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Symfony\Component\Finder\SplFileInfo;

class Logger extends AbstractLogger
{
    /** @var array */
    private $entries = [];

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return sizeof($this->getEntries()) == 0;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $string = '';
        foreach ($this->getEntries() as $level => $entries) {
            foreach ($entries as $entry) {
                $string .= '<' . $level . '>' . $this->filter($level) . '</' . $level . '>';
                $string .= "\t" . $entry['message'];
                if ($entry['context']) {
                    $string .= ' ' . implode(', ', $entry['context']);
                }
                $string .= PHP_EOL;
            }
        }

        return $string;
    }

    /**
     * @param string $level
     * @return string
     */
    private function filter($level)
    {
        if ($level == LogLevel::CRITICAL) {
            $level = 'critic.';
        }
        if ($level == LogLevel::EMERGENCY) {
            $level = 'emerg.';
        }

        return ucfirst($level);
    }

    public function log($level, $message, array $context = [])
    {
        if (!isset($this->entries[$level])) {
            $this->entries[$level] = [];
        }

        array_walk_recursive($context, [$this, 'wrapSPLFileInfo']);

        $this->entries[$level][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    /**
     * items of type SplFileInfo will be wrapped with JsonSerializableFileInfo to be serializable
     *
     * @param mixed $item
     */
    private function wrapSPLFileInfo(&$item)
    {
        if ($item instanceof SplFileInfo) {
            $item = new JsonSerializableFileInfo($item);
        }
    }
}
