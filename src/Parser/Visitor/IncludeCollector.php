<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class IncludeCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Include_) {
            $this->getAnalysis()->addInclude($node);
        }
    }
}
