<?php

namespace PhpDA\Parser;

use PhpDA\Entity\AnalysisAwareInterface;
use PhpDA\Entity\AnalysisAwareTrait;

class NodeTraverser extends \PhpParser\NodeTraverser implements AnalysisAwareInterface
{
    use AnalysisAwareTrait;

    public function traverse(array $nodes)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor instanceof AnalysisAwareInterface) {
                $visitor->setAnalysis($this->getAnalysis());
            }
        }

        return parent::traverse($nodes);
    }
}
