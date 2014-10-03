<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class UnsupportedVarCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($this->unsupports($node)) {
            $this->getAnalysis()->addUnsupportedStmt($node);
        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function unsupports(Node $node)
    {
        if ($node instanceof Node\Expr\New_) {
            return $this->unsupportsInstantiationFor($node);
        } else {
            return $this->unsupportsDynamicCallFor($node);
        }
    }

    /**
     * @param Node\Expr\New_ $node
     * @return bool
     */
    private function unsupportsInstantiationFor(Node\Expr\New_ $node)
    {
        return $node->class instanceof Node\Expr\Variable;
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function unsupportsDynamicCallFor(Node $node)
    {
        $nodeIsInspectable = (
            $node instanceof Node\Expr\Variable
            || $node instanceof Node\Expr\FuncCall
            || $node instanceof Node\Expr\StaticCall
        );

        /** @var Node\Expr\Variable $node */
        return ($nodeIsInspectable && $node->name instanceof Node\Expr\Variable);
    }
}
