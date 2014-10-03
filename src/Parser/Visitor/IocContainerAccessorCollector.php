<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class IocContainerAccessorCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\MethodCall) {
            if ($node->name === 'get' && count($node->args) > 0) {
                $this->collect($node);
            }
        }
    }
}
