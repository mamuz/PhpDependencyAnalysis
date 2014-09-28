<?php

namespace PhpDA\Command;

use PhpDA\Entity\Collection;
use PhpDA\Finder\AwareTrait as FinderAwareTrait;
use PhpDA\Parser\AwareTrait as ParserAwareTrait;
use PhpDA\Writer\AwareTrait as WriterAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Analysis extends Command
{
    use FinderAwareTrait;
    use ParserAwareTrait;
    use WriterAwareTrait;

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

        $finder = $this->getFinder();
        $finder->files()->name('*.php')->in(realpath($source))->exclude($ignore);

        $progress = $this->getHelper('progress');
        $progress->start($output, iterator_count($finder));

        $collection = new Collection;
        foreach ($finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $scriptEntity = $this->getParser()->analyze($file->getContents());
            $collection->attach($scriptEntity, $file->getRealPath());
            $progress->advance();
        }

        $this->getWriter()->write($collection)->to($format)->in($target);

        $progress->finish();
        $output->writeln('Done');
    }
}
