<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class NodeClass extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->getAnalysis()->addStmt($node);
        }
    }
}
