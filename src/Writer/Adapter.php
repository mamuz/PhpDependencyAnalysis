<?php

namespace PhpDA\Writer;

use PhpDA\Entity\AnalysisCollection;

class Adapter implements AdapterInterface
{
    /** @var LoaderInterface */
    private $strategyLoader;

    /** @var AnalysisCollection */
    private $analysisCollection;

    /** @var string */
    private $format = 'txt';

    public function __construct(LoaderInterface $pluginLoader)
    {
        $this->strategyLoader = $pluginLoader;
        $this->analysisCollection = new AnalysisCollection;
    }

    public function write(AnalysisCollection $collection)
    {
        $this->analysisCollection = $collection;
        return $this;
    }

    public function to($format)
    {
        $this->format = $format;
        return $this;
    }

    public function in($file)
    {
        file_put_contents($file, $this->createContent());
        return $this;
    }

    /**
     * @return string
     */
    private function createContent()
    {
        $strategy = $this->strategyLoader->getStrategyFor($this->format);
        return $strategy->filter($this->analysisCollection);
    }
}
