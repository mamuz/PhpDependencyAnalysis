<?php

namespace PhpDA\Parser\Visitor\Required;

use PhpParser\Node;

class DeclaredNamespaceCollector extends AbstractNamespaceCollector
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $name = new Node\Name($node->name);
            if (!$this->ignores($name)) {
                $name = $this->filter($name);
                $this->getAnalysis()->setDeclaredNamespace($name);
            }
        }
    }
}
