PhpDependencyAnalysis
=====================

[![Author](http://img.shields.io/badge/author-@mamuz_de-blue.svg?style=flat-square)](https://twitter.com/mamuz_de)
[![Build Status](https://img.shields.io/travis/mamuz/PhpDependencyAnalysis.svg?style=flat-square)](https://travis-ci.org/mamuz/PhpDependencyAnalysis)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/mamuz/PhpDependencyAnalysis.svg?style=flat-square)](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/mamuz/PhpDependencyAnalysis.svg?style=flat-square)](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis)
[![HHVM Status](https://img.shields.io/hhvm/mamuz/php-dependency-analysis.svg?style=flat-square)](http://hhvm.h4cc.de/package/mamuz/php-dependency-analysis)
[![Dependency Status](https://img.shields.io/versioneye/d/user/projects/5431680abeeeeed15600019e.svg?style=flat-square)](https://www.versioneye.com/user/projects/5431680abeeeeed15600019e)

[![Latest Stable Version](https://img.shields.io/packagist/v/mamuz/php-dependency-analysis.svg?style=flat-square)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Total Downloads](https://img.shields.io/packagist/dt/mamuz/php-dependency-analysis.svg?style=flat-square)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![License](https://img.shields.io/packagist/l/mamuz/php-dependency-analysis.svg?style=flat-square)](https://packagist.org/packages/mamuz/php-dependency-analysis)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45/big.png)](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45)

PhpDependencyAnalysis is an extendable static code analysis for object-oriented
PHP-Projects to provide [`dependency graphs`](http://en.wikipedia.org/wiki/Dependency_graph)
for abstract datatypes (Classes, Interfaces and Traits) based on [`namespaces`](http://php.net/manual/en/language.namespaces.php).
Dependencies can be aggregated to build graphs for several levels, like Package-Level or Layer-Level.
Each dependency can be verified to your defined architecture.

Read the [Introduction-Chapter](https://github.com/mamuz/PhpDependencyAnalysis/wiki/1.-Introduction) for further informations.

### Example

![](https://github.com/mamuz/PhpDependencyAnalysis/tree/master/tests/_data/svg/expectation/packages.svg)

See more [examples](https://github.com/mamuz/PhpDependencyAnalysis/wiki/4.-Examples).

## Installation

For graph creation [`GraphViz`](http://www.graphviz.org/) is required on your machine, which is
an open source graph visualization software and available for the most platforms.

After installing [`GraphViz`](http://www.graphviz.org/) the recommended way to install
[`mamuz/php-dependency-analysis`](https://packagist.org/packages/mamuz/php-dependency-analysis) is through
[composer](http://getcomposer.org/) by adding dependency to your `composer.json`:

```json
{
    "require-dev": {
        "mamuz/php-dependency-analysis": "1.*"
    }
}
```

### PHP-Version

To run the newest version of this tool, it requires PHP >= 5.5. 
For running on machines with PHP < 5.5 have a look to 0.* releases.

## Features

- Providing high customizing level
- Dependency graph creation on customized levels respectively different scopes and layers
- Supports Usage-Graph, Call-Graph and Inheritance-Graph
- Dependencies can be aggregated to a package, a module or a layer
- Detecting cycles and violations between layers in a tiered architecture
- Verifiying dependency graph against a user-defined reference architecture
- Collected Namespaces of dependencies are modifiable to meet custom use cases
- Printing graphs in several formats (HTML, SVG, DOT, JSON)
- Extandable by adding user-defined plugins for collecting and displaying dependencies
- Compatible to PHP7 Features, like [`Return Type Declarations`](https://wiki.php.net/rfc/return_types) and [`Anonymous Classes`](https://wiki.php.net/rfc/anonymous_classes)

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

## [Code of Conduct](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CODE_OF_CONDUCT.md)

As contributors and maintainers of this project you have to respect the [Code of Coduct](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CODE_OF_CONDUCT.md)

## [Changelog](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CHANGELOG.md)

See record of changes made to this project
[here](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CHANGELOG.md)

## [Contributing](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CONTRIBUTING.md)

Before opening up a pull-request please read the
[Contributing-Guideline](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CONTRIBUTING.md)

## Alternatives

Check the resources in [Code Analysis Section at Awesome PHP](https://github.com/ziadoz/awesome-php#code-analysis)
