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

namespace PhpDA\Command;

use PhpDA\Parser\Filter\NodeName;

class Config
{
    const USAGE_MODE = 'usage';

    const CALL_MODE = 'call';

    const INHERITANCE_MODE = 'inheritance';

    /** @var array */
    private $allowedModes = array(self::USAGE_MODE, self::CALL_MODE, self::INHERITANCE_MODE);

    /** @var string */
    private $mode = self::USAGE_MODE;

    /** @var string */
    private $source;

    /** @var string|array */
    private $ignore = array();

    /** @var string */
    private $formatter;

    /** @var string */
    private $target;

    /** @var string */
    private $filePattern = '*.php';

    /** @var array */
    private $visitor = array();

    /** @var array */
    private $visitorOptions = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getMode()
    {
        if (!in_array($this->mode, $this->allowedModes, true)) {
            throw new \InvalidArgumentException(
                'Config for mode must be "' . self::USAGE_MODE
                . '" or "' . self::CALL_MODE . '"'
                . '" or "' . self::INHERITANCE_MODE . '"'
            );
        }

        return $this->mode;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getFormatter()
    {
        if (!is_string($this->formatter)) {
            throw new \InvalidArgumentException('Config for Formatter must be a string');
        }

        return $this->formatter;
    }

    /**
     * @return string|array
     * @throws \InvalidArgumentException
     */
    public function getIgnore()
    {
        if (!is_string($this->ignore) && !is_array($this->ignore)) {
            throw new \InvalidArgumentException('Config for ignore must be an array or a string');
        }

        return $this->ignore;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getSource()
    {
        if (!is_string($this->source)) {
            throw new \InvalidArgumentException('Config for string must be a string');
        }

        return $this->source;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getTarget()
    {
        if (!is_string($this->target)) {
            throw new \InvalidArgumentException('Config for target must be a string');
        }

        return $this->target;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getFilePattern()
    {
        if (!is_string($this->filePattern)) {
            throw new \InvalidArgumentException('Config for filePattern must be a string');
        }

        return $this->filePattern;
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getVisitor()
    {
        if (!is_array($this->visitor)) {
            throw new \InvalidArgumentException('Config for visitor must be an array');
        }

        return $this->visitor;
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getVisitorOptions()
    {
        if (!is_array($this->visitorOptions)) {
            throw new \InvalidArgumentException('Config for visitorOptions must be an array');
        }

        return $this->visitorOptions;
    }

    /**
     * @return bool
     */
    public function hasVisitorOptionsForAggregation()
    {
        $nameFilter = new NodeName;
        foreach ($this->getVisitorOptions() as $options) {
            foreach ($options as $name => $value) {
                if (strpos($name, $nameFilter->getAggregationIndicator()) === 0 && !empty($value)) {
                    return true;
                }
            }
        }

        return false;
    }
}
