<?php

namespace PhpDA\Service;

use PhpDA\Command\Analyze;
use PhpDA\Parser\AnalyzerInterface;
use PhpDA\Writer\AdapterInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

class ApplicationFactory implements FactoryInterface
{
    /** @var FactoryInterface */
    private $finderFactory;

    /** @var FactoryInterface */
    private $analyzerFactory;

    /** @var FactoryInterface */
    private $writeAdapterFactory;

    public function __construct(
        FactoryInterface $finderFactory,
        FactoryInterface $analyzerFactory,
        FactoryInterface $writeAdapterFactory
    ) {
        $this->finderFactory = $finderFactory;
        $this->analyzerFactory = $analyzerFactory;
        $this->writeAdapterFactory = $writeAdapterFactory;
    }

    /**
     * @return Application
     */
    public function create()
    {
        $app = new Application;
        $app->add($this->createAnalyzeCommand());

        return $app;
    }

    /**
     * @return Analyze
     */
    protected function createAnalyzeCommand()
    {
        $command = new Analyze;
        $command->setFinder($this->createFinder());
        $command->setAnalyzer($this->createAnalyzer());
        $command->setWriteAdapter($this->createWriteAdapter());

        return $command;
    }

    /**
     * @return Finder
     */
    private function createFinder()
    {
        return $this->finderFactory->create();
    }

    /**
     * @return AnalyzerInterface
     */
    private function createAnalyzer()
    {
        return $this->analyzerFactory->create();
    }

    /**
     * @return AdapterInterface
     */
    private function createWriteAdapter()
    {
        return $this->writeAdapterFactory->create();
    }
}
