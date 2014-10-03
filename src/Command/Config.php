<?php

namespace PhpDA\Command;

class Config
{
    /** @var string */
    private $source;

    /** @var string|array */
    private $ignore = array();

    /** @var string */
    private $formatter;

    /** @var string */
    private $target;

    /** @var array */
    private $visitor = array();

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
}
