<?php

namespace PhpDA\Parser;

use PhpDA\Entity\AnalysisAwareInterface;
use PhpDA\Entity\AnalysisAwareTrait;
use PhpDA\Plugin\LoaderInterface;
use PhpParser\NodeVisitor;

class NodeTraverser extends \PhpParser\NodeTraverser implements AnalysisAwareInterface, TraverseInterface
{
    use AnalysisAwareTrait;

    /** @var array */
    private $requiredVisitors = array(
        'PhpParser\NodeVisitor\NameResolver',
        'PhpDA\Parser\Visitor\NamespaceCollector',
    );

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
        $visitors = $this->filterVisitors($visitors);
        foreach ($visitors as $fqn) {
            $this->addVisitor($this->loadVisitorBy($fqn));
        }
    }

    /**
     * @param array $visitors
     * @return array
     */
    private function filterVisitors(array $visitors)
    {
        $fqns = $this->requiredVisitors;
        foreach ($visitors as $fqn) {
            $fqn = trim($fqn, '\\');
            if (!in_array($fqn, $fqns)) {
                $fqns[] = $fqn;
            }
        }

        return $fqns;
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
