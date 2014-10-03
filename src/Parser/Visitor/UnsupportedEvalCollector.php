<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class UnsupportedEvalCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Eval_) {
            $this->collect($node);
        }
    }
}
