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
            if (!$this->ignores($node)) {
                $this->getAnalysis()->addNamespace($node);
            }
        }
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
