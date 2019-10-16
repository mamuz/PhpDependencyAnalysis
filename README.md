PhpDependencyAnalysis
=====================

[![Author](http://img.shields.io/badge/author-@mamuz_de-blue.svg?style=flat-square)](https://twitter.com/mamuz_de)
[![Build Status](https://img.shields.io/travis/mamuz/PhpDependencyAnalysis.svg?style=flat-square)](https://travis-ci.org/mamuz/PhpDependencyAnalysis)
[![Latest Stable Version](https://img.shields.io/packagist/v/mamuz/php-dependency-analysis.svg?style=flat-square)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Total Downloads](https://img.shields.io/packagist/dt/mamuz/php-dependency-analysis.svg?style=flat-square)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![License](https://img.shields.io/packagist/l/mamuz/php-dependency-analysis.svg?style=flat-square)](https://packagist.org/packages/mamuz/php-dependency-analysis)

PhpDependencyAnalysis is an extendable static code analysis for object-oriented
PHP-Projects to generate [`dependency graphs`](http://en.wikipedia.org/wiki/Dependency_graph)
from abstract datatypes (Classes, Interfaces and Traits) based on [`namespaces`](http://php.net/manual/en/language.namespaces.php).
Dependencies can be aggregated to build graphs for several levels, like Package-Level or Layer-Level.
Each dependency can be verified to a defined architecture.

Read the [Introduction-Chapter](https://github.com/mamuz/PhpDependencyAnalysis/wiki/1.-Introduction) for further informations.

### Example

![](https://cdn.rawgit.com/mamuz/PhpDependencyAnalysis/master/tests/_data/svg/expectation/packages.svg)

See more [examples](https://github.com/mamuz/PhpDependencyAnalysis/wiki/4.-Examples).

## Installation

### As a Docker Image (recommend way)

```bash
docker pull mamuz/phpda
```

### As a Composer Dependency

**NOTE:** For graph creation [`GraphViz`](http://www.graphviz.org/) is required on your machine, which is
an open source graph visualization software and available for the most platforms.

```sh
$ composer require --dev mamuz/php-dependency-analysis
```

### As a Phar

Since version 2.0.0 not supported anymore.

## Features

- High customizing level
- Graph creation on customized levels respectively different scopes and layers
- Supports Usage-Graph, Call-Graph and Inheritance-Graph
- Dependencies can be aggregated such as to a package, a module or a layer
- Detecting cycles and violations between layers in a tiered architecture
- Verifiying dependency graph against a user-defined reference architecture
- Collected namespaces of dependencies are modifiable to meet custom use cases
- Printing graphs in several formats (HTML, SVG, DOT, JSON)
- Extandable by adding user-defined plugins for collecting and displaying
- Compatible to PHP7 Features, like [`Return Type Declarations`](https://wiki.php.net/rfc/return_types) and [`Anonymous Classes`](https://wiki.php.net/rfc/anonymous_classes)

## Usage

Phpda can run out of the box by using a prepared [`configuration`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/phpda.yml.dist).
As you can see configuration is defined by a [`YAML`](http://en.wikipedia.org/wiki/YAML) file.

To provide your own configuration create a yml file, e.g. located in `./phpda.yml`:

```yml
mode: 'usage'
source: './src'
filePattern: '*.php'
ignore: 'tests'
formatter: 'PhpDA\Writer\Strategy\Svg'
target: './phpda.svg'
groupLength: 1
visitor:
  - PhpDA\Parser\Visitor\TagCollector
  - PhpDA\Parser\Visitor\SuperglobalCollector
visitorOptions:
  PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector: {minDepth: 2, sliceLength: 2}
  PhpDA\Parser\Visitor\Required\MetaNamespaceCollector: {minDepth: 2, sliceLength: 2}
  PhpDA\Parser\Visitor\Required\UsedNamespaceCollector: {minDepth: 2, sliceLength: 2}
  PhpDA\Parser\Visitor\TagCollector: {minDepth: 2, sliceLength: 2}
```

Perform an analysis with that configuration:

```sh
$ docker run --rm -v $PWD:/app mamuz/phpda
```

Read the [Configuration-Chapter](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration)
to get knowledge about all available options.

## [Wiki](https://github.com/mamuz/PhpDependencyAnalysis/wiki)

1. [Introduction](https://github.com/mamuz/PhpDependencyAnalysis/wiki/1.-Introduction)
2. [Requirements](https://github.com/mamuz/PhpDependencyAnalysis/wiki/2.-Requirements)
3. [Configuration](https://github.com/mamuz/PhpDependencyAnalysis/wiki/3.-Configuration)
4. [Examples](https://github.com/mamuz/PhpDependencyAnalysis/wiki/4.-Examples)
5. [Plugins](https://github.com/mamuz/PhpDependencyAnalysis/wiki/5.-Plugins)

## [Code of Conduct](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/.github/CODE_OF_CONDUCT.md)

As contributors and maintainers of this project you have to respect the [Code of Coduct](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/.github/CODE_OF_CONDUCT.md)

## [Changelog](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CHANGELOG.md)

See record of changes made to this project
[here](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/CHANGELOG.md)

## [Contributing](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/.github/CONTRIBUTING.md)

Before opening up a pull-request please read the
[Contributing-Guideline](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/.github/CONTRIBUTING.md)

## Alternatives

Check the resources in [Satic Analysis Section at Awesome PHP](https://github.com/ziadoz/awesome-php#static-analysis)
