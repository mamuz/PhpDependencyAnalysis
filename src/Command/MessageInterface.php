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

interface MessageInterface
{
    const VERSION = 'v0.6.1';

    const NAME = 'PhpDependencyAnalysis by Marco Muths';

    const COMMAND = 'analyze';

    const HELP = 'Please visit <info>https://github.com/mamuz/PhpDependencyAnalysis</info> for detailed informations.';

    const ARGUMENT_CONFIG = 'Path to yaml configuration file.';

    const OPTION_MODE = 'Analysis mode: "usage" (default) or "call" or "inheritance"';

    const OPTION_SOURCE = 'Directory to analyze.';

    const OPTION_FILE_PATTERN = 'Pattern to match files for analysis.';

    const OPTION_IGNORE = 'Exclude directories from source for analysis.';

    const OPTION_FORMATTER = 'Formatter as FQCN for creating dependency graph.';

    const OPTION_TARGET = 'Filepath for writing created dependency graph.';

    const READ_CONFIG_FROM = 'Configuration read from ';

    const WRITE_GRAPH_TO = 'Write dependency graph to ';

    const PROGRESS_DISPLAY = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% Memory: %memory:6s%';

    const PARSE_LOGS = 'Logs:';

    const NOTHING_TO_PARSE = '<error>No files found!</error>';

    const DONE = 'Done.';
}
