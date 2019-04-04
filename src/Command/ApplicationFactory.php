<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 Marco Muths
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

use Composer\Autoload\ClassLoader;
use PhpDA\Command\MessageInterface as Message;
use PhpDA\Plugin\FactoryInterface;
use PhpDA\Plugin\Loader;
use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Parser;

class ApplicationFactory implements FactoryInterface
{
    /** @var ClassLoader */
    public static $classLoader;

    /**
     * @param ClassLoader $classLoader
     */
    public function __construct(ClassLoader $classLoader)
    {
        self::$classLoader = $classLoader;
    }

    /**
     * @return Application
     */
    public function create()
    {
        $app = new Application(Message::NAME, Version::read());
        $app->setDefaultCommand(Message::CMD_ANALYZE);
        $app->add($this->createAnalyzeCommand());
        $app->add($this->createRenderCommand());

        if (\Phar::running()) {
            $app->add($this->createUpdateCommand());
        }

        return $app;
    }

    /**
     * @return Analyze
     */
    protected function createAnalyzeCommand()
    {
        $command = new Analyze(Message::CMD_ANALYZE);

        $command->setHelp(Message::CMD_ANALYZE_HELP);
        $command->setDescription(Message::CMD_ANALYZE_DESCR);
        $command->setConfigParser(new Parser);
        $command->setStrategyLoader(new Loader);

        return $command;
    }

    /**
     * @return Render
     */
    protected function createRenderCommand()
    {
        $command = new Render(Message::CMD_RENDER);

        $command->setHelp(Message::CMD_RENDER_HELP);
        $command->setDescription(Message::CMD_RENDER_DESCR);
        $command->setConfigParser(new Parser);
        $command->setStrategyLoader(new Loader);

        return $command;
    }

    /**
     * @return Update
     */
    protected function createUpdateCommand()
    {
        $command = new Update(Message::CMD_UPDATE);

        $command->setDescription(MessageInterface::CMD_UPDATE_DESCR);
        $command->setHelp(Message::CMD_UPDATE_HELP);

        return $command;
    }
}
