<?php

namespace PhpDA\Parser\Visitor;

use PhpDA\Entity\AnalysisAwareInterface;
use PhpDA\Entity\AnalysisAwareTrait;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

abstract class AbstractVisitor extends NodeVisitorAbstract implements AnalysisAwareInterface
{
    use AnalysisAwareTrait;

    /**
     * @param Node $stmt
     * @return void
     */
    protected function collect(Node $stmt)
    {
        $this->getAnalysis()->addStmt($stmt, get_class($this));
    }
}
