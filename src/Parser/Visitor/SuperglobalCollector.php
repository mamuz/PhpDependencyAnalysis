<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class SuperglobalCollector extends AbstractVisitor
{
    /** @var array */
    private $vars = array(
        'GLOBALS',
        '_SERVER',
        '_GET',
        '_POST',
        '_FILES',
        '_COOKIE',
        '_SESSION',
        '_REQUEST',
        '_ENV',
    );

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Variable) {
            if ($this->match($node)) {
                $this->collect($node);
            }
        }
    }

    /**
     * @param Node\Expr\Variable $var
     * @return bool
     */
    private function match(Node\Expr\Variable $var)
    {
        return in_array($var->name, $this->vars);
    }
}
