<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class UnsupportedFuncCollector extends AbstractVisitor
{
    /** @var array */
    private $unsupportedFuncs = array(
        'call_user_func',
        'call_user_func_array',
        'call_user_method',
        'call_user_method_array',
        'forward_static_call',
        'forward_static_call_array',
        'create_function',
    );

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall) {
            if ($this->unsupports($node)) {
                $this->getAnalysis()->addUnsupportedStmt($node);
            }
        }
    }

    /**
     * @param Node\Expr\FuncCall $funcCall
     * @return bool
     */
    private function unsupports(Node\Expr\FuncCall $funcCall)
    {
        $name = $funcCall->name;
        if (!$name instanceof Node\Name) {
            return false;
        }

        return in_array($name->toString(), $this->unsupportedFuncs);
    }
}
