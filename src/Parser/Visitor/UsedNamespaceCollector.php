<?php

namespace PhpDA\Parser\Visitor;

use PhpDA\Plugin\ConfigurableInterface;
use PhpParser\Node;

class UsedNamespaceCollector extends AbstractVisitor implements ConfigurableInterface
{
    /** @var int|null */
    private $offset;

    /** @var int|null */
    private $length;

    /** @var array */
    private $ignoredNamespaces = array('self', 'static');

    public function setOptions(array $options)
    {
        if (isset($options['offset'])) {
            $this->offset = (int) $options['offset'];
        }

        if (isset($options['length'])) {
            $this->length = (int) $options['length'];
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Name) {
            if (!$this->ignores($node)) {
                $node = $this->filter($node);
                $this->getAnalysis()->addUsedNamespace($node);
            }
        }
    }

    /**
     * @param Node\Name $name
     * @return Node\Name
     */
    private function filter(Node\Name $name)
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

    /**
     * @param Node\Name $name
     * @return bool
     */
    private function ignores(Node\Name $name)
    {
        return in_array($name->toString(), $this->ignoredNamespaces);
    }
}
