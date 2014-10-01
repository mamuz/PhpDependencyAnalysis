<?php

namespace PhpDA\Command;

use PhpDA\Parser\AnalyzerInterface;
use PhpDA\Writer\AdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Analyze extends Command
{
    /** @var Finder|\Symfony\Component\Finder\SplFileInfo[] */
    private $finder;

    /** @var AnalyzerInterface */
    private $analyzer;

    /** @var AdapterInterface */
    private $writeAdapter;

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
        $this->setName("phpda:analysis")
            ->setDescription("Report dependencies of directory")
            ->addArgument(
                'source',
                InputArgument::OPTIONAL,
                'Directory to parse, default is current path',
                '.'
            )
            ->addOption(
                'ignore',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Excluding directories'
            )
            ->addOption(
                'target',
                't',
                InputOption::VALUE_OPTIONAL,
                'Excluding directories',
                './phpda'
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Excluding directories',
                'txt'
            )
            ->setHelp('....');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $ignore = $input->getOption('ignore');
        $target = $input->getOption('target');
        $format = $input->getOption('format');

        $this->finder->files()->name('*.php')->in(realpath($source))->exclude($ignore);

        $progress = $this->getHelper('progress');
        $progress->start($output, iterator_count($this->finder));

        foreach ($this->finder as $file) {
            $this->analyzer->analyze($file);
            $progress->advance();
        }

        $this->writeAdapter->write($this->analyzer->getAnalysisCollection())->to($format)->in($target);

        $progress->finish();
        $output->writeln('Done');
    }
}
