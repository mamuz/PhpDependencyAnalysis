<?php

namespace PhpDA\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\AbstractVisitor;
use PhpParser\Node;

class DeclaredNamespaceCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $name = new Node\Name($node->name);
            $this->getAnalysis()->setDeclaredNamespace($name);
        }
    }
}
