<?php

namespace PhpDA\Command;

use PhpDA\Parser\AnalyzerInterface;
use PhpDA\Writer\AdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class Analyze extends Command
{
    /** @var Config */
    private $config;

    /** @var Parser */
    private $configParser;

    /** @var Finder|\Symfony\Component\Finder\SplFileInfo[] */
    private $finder;

    /** @var AnalyzerInterface */
    private $analyzer;

    /** @var AdapterInterface */
    private $writeAdapter;

    /**
     * @param Parser $parser
     * @return void
     */
    public function setConfigParser(Parser $parser)
    {
        $this->configParser = $parser;
    }

    /**
     * @param Finder $finder
     * @return void
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param AdapterInterface $writeAdapter
     * @return void
     */
    public function setWriteAdapter(AdapterInterface $writeAdapter)
    {
        $this->writeAdapter = $writeAdapter;
    }

    /**
     * @param AnalyzerInterface $analyzer
     * @return void
     */
    public function setAnalyzer(AnalyzerInterface $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    protected function configure()
    {
        $this->setName("analyze")
            ->setDescription("Analyze php dependencies")
            ->addArgument(
                'config',
                InputArgument::OPTIONAL,
                'Path to yaml configuration file',
                'phpda.yml'
            )
            ->setHelp('....');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bindConfigBy($input);

        $progress = $this->getHelper('progress');
        $progress->start($output, iterator_count($this->finder));

        foreach ($this->finder as $file) {
            $this->analyzer->analyze($file);
            $progress->advance();
        }
        $this->writeAnalysis();

        $progress->finish();
        $output->writeln('Done');
    }

    /**
     * @param InputInterface $input
     * @throws \InvalidArgumentException
     * @return void
     */
    private function bindConfigBy(InputInterface $input)
    {
        $configFile = $input->getArgument('config');
        $config = $this->configParser->parse(file_get_contents($configFile));

        if (!is_array($config)) {
            throw new \InvalidArgumentException('Configuration is invalid');
        }

        $this->config = new Config($config);

        $this->finder
            ->files()
            ->name('*.php')
            ->in(realpath($this->config->getSource()))
            ->exclude($this->config->getIgnore());

        $this->analyzer->getTraverser()->bindVisitors(
            $this->config->getVisitor(),
            $this->config->getVisitorOptions()
        );
    }

    /**
     * @return void
     */
    private function writeAnalysis()
    {
        $this->writeAdapter
            ->write($this->analyzer->getAnalysisCollection())
            ->with($this->config->getFormatter())
            ->to($this->config->getTarget());
    }
}
