<?php

namespace PhpDA\Parser;

use PhpDA\Entity\AnalysisAwareInterface;
use PhpDA\Entity\AnalysisAwareTrait;
use PhpDA\Plugin\LoaderInterface;
use PhpParser\NodeVisitor;

class NodeTraverser extends \PhpParser\NodeTraverser implements AnalysisAwareInterface, TraverseInterface
{
    use AnalysisAwareTrait;

    /** @var LoaderInterface */
    private $visitorLoader;

    /**
     * @param LoaderInterface $visitorLoader
     * @return NodeTraverser
     */
    public function setVisitorLoader(LoaderInterface $visitorLoader)
    {
        $this->visitorLoader = $visitorLoader;
        return $this;
    }

    public function bindVisitors(array $visitors)
    {
        foreach ($visitors as $fqn) {
            $this->addVisitor($this->loadVisitorBy($fqn));
        }
    }

    /**
     * @param string $fqn
     * @throws \RuntimeException
     * @return NodeVisitor
     */
    private function loadVisitorBy($fqn)
    {
        $visitor = $this->visitorLoader->get($fqn);

        if (!$visitor instanceof NodeVisitor) {
            throw new \RuntimeException('Visitor ' . $fqn . ' is not an instance of NodeVisitor');
        }

        return $visitor;
    }

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
