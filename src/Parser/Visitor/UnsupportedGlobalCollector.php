<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class UnsupportedGlobalCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Global_) {
            foreach ($node->vars as $var) {
                $this->collect($var);
            }
        }
    }
}
