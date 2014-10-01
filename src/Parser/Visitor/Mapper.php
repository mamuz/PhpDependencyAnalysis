<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class Mapper extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        // @todo
        if ($node instanceof Node\Name) {
            return new Node\Name($node->toString('_'));
        }
    }
}
