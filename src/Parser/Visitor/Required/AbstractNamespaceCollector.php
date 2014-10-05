<?php

namespace PhpDA\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\AbstractVisitor;
use PhpDA\Plugin\ConfigurableInterface;
use PhpParser\Node;

abstract class AbstractNamespaceCollector extends AbstractVisitor implements ConfigurableInterface
{
    /** @var int|null */
    private $offset;

    /** @var int|null */
    private $length;

    /** @var int */
    private $minLength = 0;

    public function setOptions(array $options)
    {
        if (isset($options['offset'])) {
            $this->offset = (int) $options['offset'];
        }

        if (isset($options['length'])) {
            $this->length = (int) $options['length'];
        }

        if (isset($options['minLength'])) {
            $this->minLength = (int) $options['minLength'];
        }
    }

    /**
     * @param Node\Name $name
     * @return bool
     */
    protected function ignores(Node\Name $name)
    {
        if ($this->minLength > 0) {
            return count($name->parts) < $this->minLength;
        }

        return false;
    }

    /**
     * @param Node\Name $name
     * @return Node\Name
     */
    protected function filter(Node\Name $name)
    {
        if (is_null($this->offset) && is_null($this->length)) {
            return $name;
        }

        if (is_null($this->length)) {
            $parts = array_slice($name->parts, (int) $this->offset);
        } else {
            $parts = array_slice($name->parts, (int) $this->offset, (int) $this->length);
        }

        return new Node\Name($parts);
    }
}
