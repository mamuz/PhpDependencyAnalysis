<?php

namespace PhpDA\Parser\Visitor;

use PhpDA\Entity\Analysis;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class Mapper extends NodeVisitorAbstract
{
    /** @var Analysis */
    private $analysis;

    /**
     * @return Analysis
     */
    public function getScript()
    {
        return $this->analysis;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->analysis = new Analysis;
        $this->analysis->setStmts($nodes);
    }

    public function leaveNode(Node $node)
    {
        // @todo
        if ($node instanceof Node\Name) {
            return new Node\Name($node->toString('_'));
        }
    }
}
