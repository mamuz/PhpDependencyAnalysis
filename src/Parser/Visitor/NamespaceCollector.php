<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class NamespaceCollector extends AbstractVisitor
{
    /** @var array */
    private $ignoredNamespaces = array('self', 'static');

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Name) {
            if (!$this->ignores($node->toString())) {
                $this->getAnalysis()->addNamespace($node);
            }
        }
    }

    /**
     * @param string $namespace
     * @return bool
     */
    private function ignores($namespace)
    {
        return in_array($namespace, $this->ignoredNamespaces);
    }
}
