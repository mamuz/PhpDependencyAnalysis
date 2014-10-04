<?php

namespace PhpDA\Parser\Visitor;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class MultiNamespaceDetector extends NodeVisitorAbstract
{
    /** @var int */
    private $count = 0;

    /**
     * {@inheritdoc}
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function beforeTraverse(array $nodes)
    {
        $this->count = 0;
    }

    /**
     * {@inheritdoc}
     * @throws Error
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->count++;
        }

        if ($this->count > 1) {
            throw new Error('Script contains more than one namespace declarations');
        }
    }
}
