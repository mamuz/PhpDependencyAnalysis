<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Command;

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected function configure()
    {
        $this->addOption('rollback', 'r', InputOption::VALUE_NONE, MessageInterface::OPTION_ROLLBACK);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new Updater;
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setPackageName('mamuz/php-dependency-analysis');
        $updater->getStrategy()->setPharName('bin/phpda.phar');
        $updater->getStrategy()->setCurrentLocalVersion(MessageInterface::VERSION);

        if ($input->getOption('rollback')) {
            $updater->rollback();
            $output->writeln(MessageInterface::ROLLBACK_SUCCESS . PHP_EOL);
        } elseif ($result = $updater->update()) {
            $output->writeln(MessageInterface::UPDATE_SUCCESS . $updater->getNewVersion() . PHP_EOL);
        } else {
            $output->writeln(MessageInterface::UPDATE_NOT_NEEDED . PHP_EOL);
        }
    }
}
