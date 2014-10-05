<?php

namespace PhpDA\Parser\Visitor;

use PhpDA\Plugin\ConfigurableInterface;
use PhpParser\Node;

class ShellExecCollector extends AbstractVisitor implements ConfigurableInterface
{
    /** @var array */
    private $shellFuncs = array(
        'exec',
        'passthru',
        'proc_open',
        'shell_exec',
        'system',
    );

    public function setOptions(array $options)
    {
        if (isset($options['shellFuncs'])) {
            $this->shellFuncs = (array) $options['shellFuncs'];
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\ShellExec
            || ($node instanceof Node\Expr\FuncCall && $this->supports($node))
        ) {
            $this->collect($node);
        }
    }

    /**
     * @param Node\Expr\FuncCall $funcCall
     * @return bool
     */
    private function supports(Node\Expr\FuncCall $funcCall)
    {
        $name = $funcCall->name;
        if (!$name instanceof Node\Name) {
            return false;
        }

        return in_array($name->toString(), $this->shellFuncs);
    }
}
