PhpDependencyAnalysis
=====================

[![Build Status](https://travis-ci.org/mamuz/PhpDependencyAnalysis.svg?branch=master)](https://travis-ci.org/mamuz/PhpDependencyAnalysis)
[![Coverage Status](https://img.shields.io/coveralls/mamuz/PhpDependencyAnalysis.svg)](https://coveralls.io/r/mamuz/PhpDependencyAnalysis?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45/mini.png)](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45)
[![HHVM Status](http://hhvm.h4cc.de/badge/mamuz/php-dependency-analysis.png)](http://hhvm.h4cc.de/package/mamuz/php-dependency-analysis)
[![Dependency Status](https://www.versioneye.com/user/projects/5431680abeeeeed15600019e/badge.svg)](https://www.versioneye.com/user/projects/5431680abeeeeed15600019e)

[![Latest Stable Version](https://poser.pugx.org/mamuz/php-dependency-analysis/v/stable.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Latest Unstable Version](https://poser.pugx.org/mamuz/php-dependency-analysis/v/unstable.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Total Downloads](https://poser.pugx.org/mamuz/php-dependency-analysis/downloads.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![License](https://poser.pugx.org/mamuz/php-dependency-analysis/license.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)

PhpDependencyAnalysis is an extendable static code analysis for object-oriented
PHP-Projects to provide a [`dependency graph`](http://en.wikipedia.org/wiki/Dependency_graph)
for packages or for abstract datatypes (Classes, Interfaces and Traits) based on [`namespaces`](http://php.net/manual/en/language.namespaces.php).

Read the [Introduction-Chapter](https://github.com/mamuz/PhpDependencyAnalysis/wiki/1.-Introduction) for further informations.

### Example

![](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/examples/packages.png)

## Installation

For graph creation [`GraphViz`](http://www.graphviz.org/) is required on your machine, which is
an open source graph visualization software and available for the most platforms.

After installing [`GraphViz`](http://www.graphviz.org/) the recommended way to install
[`mamuz/php-dependency-analysis`](https://packagist.org/packages/mamuz/php-dependency-analysis) is through
[composer](http://getcomposer.org/) by adding dependency to your `composer.json`:

```json
{
    "require-dev": {
        "mamuz/php-dependency-analysis": "0.1.*"
    }
}
```

## Features

- Providing high customizing level
- Dependency graph creation on customized levels respectively different scopes and layers
- Supports Usage-Graph, Call-Graph and Inheritance-Graph
- Dependencies can be aggregated to a package, a module or a layer
- Detecting cycles and violations between layers in a tiered architecture
- Printing graphs in several formats (HTML, SVG, DOT)
- Extandable by adding user-defined plugins for collecting and printing of dependencies

## Configuration

This tool is configurable by a [`YAML`](http://en.wikipedia.org/wiki/YAML) file.
You can copy a prepared file from the vendor directory.

```sh
cp ./vendor/mamuz/php-dependency-analysis/phpda.yml.dist ./myconfig.yml
```

See [prepared configuration](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/phpda.yml.dist)
and read the [Configuration-Chapter](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration) for available options.

## Usage

Run this command line to create a dependecy graph:

```sh
./vendor/bin/phpda analyze ./myconfig.yml
```

After that open created report file with your prefered tool.

## [Wiki](https://github.com/mamuz/PhpDependencyAnalysis/wiki)

1. [Introduction](https://github.com/mamuz/PhpDependencyAnalysis/wiki/1.-Introduction)
2. [Requirements](https://github.com/mamuz/PhpDependencyAnalysis/wiki/2.-Requirements)
3. [Configuration](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration)
4. [Examples](https://github.com/mamuz/PhpDependencyAnalysis/wiki/4.-Examples)
5. [Plugins](https://github.com/mamuz/PhpDependencyAnalysis/wiki/5.-Plugins)

## [Contributing](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CONTRIBUTING.md)

Before opening up a pull-request please read the
[Contributing-Guideline](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CONTRIBUTING.md)
