<?php

namespace PhpDA\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Analysis extends Command
{
    const DEFAULT_SOURCE = '.';

    const DEFAULT_TARGET = './phpda';

    const DEFAULT_FORMAT = 'txt';

    protected function configure()
    {
        $this->setName("phpda:analysis")
            ->setDescription("Report dependencies of directory")
            ->addArgument(
                'source',
                InputArgument::OPTIONAL,
                'Directory to parse, default is current path',
                self::DEFAULT_SOURCE
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
                self::DEFAULT_TARGET
            )
            ->addOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Excluding directories',
                self::DEFAULT_FORMAT
            )
            ->setHelp('....');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $ignore = $input->getOption('ignore');
        $target = $input->getOption('target');
        $format = $input->getOption('format');

        $finder = new Finder();
        $finder->files()->name('*.php')->in(realpath($source))->exclude($ignore);

        $progress = $this->getHelper('progress');
        $progress->start($output, iterator_count($finder));

        foreach ($finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $file->getRealpath();
            $this->parse($file->getContents());
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('Done');
    }

    private function parse($code)
    {
        $parser = new \PhpParser\Parser(new \PhpParser\Lexer\Emulative);

        try {
            $stmts = $parser->parse($code);
            print_r($stmts);
        } catch (\PhpParser\Error $e) {
            print 'Parse Error: '. $e->getMessage() . PHP_EOL;
        }
    }
}
