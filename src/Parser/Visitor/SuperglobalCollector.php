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
            if ($this->match($node->name)) {
                $this->getAnalysis()->addSuperglobal($node);
            }
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    private function match($name)
    {
        return in_array($name, $this->vars);
    }
}
