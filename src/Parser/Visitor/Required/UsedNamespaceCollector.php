<?php

namespace PhpDA\Parser\Visitor\Required;

use PhpParser\Node;

class UsedNamespaceCollector extends AbstractNamespaceCollector
{
    /** @var array */
    private $ignoredNamespaces = array('self', 'static');

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Name) {
            if (!$this->ignores($node)) {
                $node = $this->filter($node);
                $this->getAnalysis()->addUsedNamespace($node);
            }
        }
    }

    protected function ignores(Node\Name $name)
    {
        if ($ignores = parent::ignores($name)) {
            return $ignores;
        }

        return in_array($name->toString(), $this->ignoredNamespaces);
    }
}
