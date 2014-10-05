<?php

namespace PhpDA\Parser\Visitor;

use PhpDA\Plugin\ConfigurableInterface;
use PhpParser\Node;

class UnsupportedFuncCollector extends AbstractVisitor implements ConfigurableInterface
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

    public function setOptions(array $options)
    {
        if (isset($options['unsupportedFuncs'])) {
            $this->unsupportedFuncs = (array) $options['unsupportedFuncs'];
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall) {
            if ($this->unsupports($node)) {
                $this->collect($node);
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
