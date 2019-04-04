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

interface MessageInterface
{
    const NAME = 'PhpDependencyAnalysis by Marco Muths';

    const CMD_ANALYZE = 'analyze';

    const CMD_ANALYZE_HELP = "Perform a dependency analysis and output the result.\n\nphpda analyze myconfig.yml\n\nPlease visit <info>https://github.com/mamuz/PhpDependencyAnalysis</info> for more informations.";

    const CMD_ANALYZE_DESCR = 'Analyze source directory based on configuration';

    const CMD_ANALYZE_ARG_CONFIG = 'Path to yaml configuration file.';

    const CMD_ANALYZE_OPT_MODE = 'Analysis mode: "usage" (default) or "call" or "inheritance"';

    const CMD_ANALYZE_OPT_SOURCE = 'Directory to analyze.';

    const CMD_ANALYZE_OPT_FILE_PATTERN = 'Pattern to match files for analysis.';

    const CMD_ANALYZE_OPT_IGNORE = 'Exclude directories from source for analysis.';

    const CMD_ANALYZE_OPT_FORMATTER = 'Formatter as FQCN for creating dependency graph.';

    const CMD_ANALYZE_OPT_TARGET = 'Filepath for writing created dependency graph.';

    const CMD_RENDER = 'render';

    const CMD_RENDER_HELP = "Read the result of an analysis from a JSON file and render it in the desired format.";

    const CMD_RENDER_DESCR = 'Render a previously created JSON file';

    const CMD_RENDER_ARG_SOURCE = 'The JSON file to load.';

    const CMD_RENDER_ARG_TARGET = 'Filepath for writing created dependency graph.';

    const CMD_RENDER_OPT_CONFIG = 'Path to the yaml configuration file.';

    const CMD_RENDER_OPT_FORMATTER = 'Formatter as FQCN for creating dependency graph.';

    const CMD_UPDATE = 'update';

    const CMD_UPDATE_DESCR = 'Update to latest version';

    const CMD_UPDATE_HELP = "The update command checks github.com for newer version and if found, installs the latest.\n\nphpda update";

    const CMD_UPDATE_OPT_ROLLBACK = 'Revert to an older installation';

    const READ_CONFIG_FROM = 'Configuration read from %s';

    const RENDER_FROM_TO = 'Render data from %s to %s';

    const WRITE_GRAPH_TO = 'Write dependency graph to %s';

    const PROGRESS_DISPLAY = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% Memory: %memory:6s%';

    const PARSE_LOGS = 'Logs:';

    const NOTHING_TO_PARSE = '<error>No files found!</error>';

    const UPDATE_SUCCESS = '<fg=green>Update done. Installed new Version is %s</>';

    const UPDATE_NOT_NEEDED = '<info>Current installation is already the latest version</info>';

    const ROLLBACK_SUCCESS = '<fg=green>Rollback to the last version done</>';

    const DONE = '<fg=green>Done</>';
}
