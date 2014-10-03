<?php

namespace PhpDA\Writer;

use PhpDA\Entity\AnalysisCollection;
use PhpDA\Plugin\LoaderInterface;
use PhpDA\Writer\Strategy\StrategyInterface;

class Adapter implements AdapterInterface
{
    /** @var LoaderInterface */
    private $strategyLoader;

    /** @var AnalysisCollection */
    private $analysisCollection;

    /** @var string */
    private $fqn;

    public function __construct(LoaderInterface $loader)
    {
        $this->strategyLoader = $loader;
        $this->analysisCollection = new AnalysisCollection;
    }

    public function write(AnalysisCollection $collection)
    {
        $this->analysisCollection = $collection;
        return $this;
    }

    public function with($fqn)
    {
        $this->fqn = $fqn;
        return $this;
    }

    public function to($file)
    {
        file_put_contents($file, $this->createContent());
        return $this;
    }

    /**
     * @return string
     */
    private function createContent()
    {
        return $this->loadStrategy()->filter($this->analysisCollection);
    }

    /**
     * @throws \RuntimeException
     * @return StrategyInterface
     */
    private function loadStrategy()
    {
        $strategy = $this->strategyLoader->get($this->fqn);

        if (!$strategy instanceof StrategyInterface) {
            throw new \RuntimeException('Strategy ' . $this->fqn . ' is not an instance of StrategyInterface');
        }

        return $strategy;
    }
}
