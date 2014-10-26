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

PhpDependencyAnalysis is an extandable static code analysis for
PHP-Projects (>= 5.3.3) to provide a [`dependency graph`](http://en.wikipedia.org/wiki/Dependency_graph)
for abstract Datatypes (Classes, Interfaces and Traits) based on [`namespaces`](http://php.net/manual/en/language.namespaces.php).
It creates dependency graphs on customizable levels, e.g. on package-level or on class-level.
Thus, it's usable to declare dependencies in general, but it's also usable to
perform a detection of violations between layers in a tiered architecture according to
compliance with [`SoC (Separation of Concerns)`](http://en.wikipedia.org/wiki/Separation_of_concerns),
[`LoD (Law of Demeter)`](http://en.wikipedia.org/wiki/Law_of_Demeter) and
[`ADP (Acyclic Dependencies Principle)`](http://en.wikipedia.org/wiki/Acyclic_dependencies_principle).
For huge PHP-Projects it's recommend to integrate it to your [`CI`](http://en.wikipedia.org/wiki/Continuous_integration)
to monitor dependencies and violations.

## Installation

For graph creating [`GraphViz`](http://www.graphviz.org/) is required on your machine, which is
an open source graph visualization software and available for the most platforms.

After installing [`GraphViz`](http://www.graphviz.org/) the recommended way to install
[`mamuz/php-dependency-analysis`](https://packagist.org/packages/mamuz/php-dependency-analysis) is through
[composer](http://getcomposer.org/) by adding dependency to your `composer.json`:

```json
{
    "require-dev": {
        "mamuz/php-dependency-analysis": "dev-master"
    }
}
```

## Features

- Providing high customizing level
- Creating dependency graphs on customized levels respectively different scopes
- Detecting cycles and violations between layers in a tiered architecture
- Printing graphs in several formats (HTML, SVG, DOT)
- Adding user-defined detection plugins
- Adding user-defined output plugins for printing graphs
- Supporting collecting namespaces from [`IoC-Containers`](http://en.wikipedia.org/wiki/Inversion_of_control)
- Supporting collecting [`PHP-Superglobals`](http://php.net/manual/en/language.variables.superglobals.php) as a dependency
- Supporting collecting PHP-Statements, which cannot be resolved, like `create_function` or `eval`
- Supporting collecting namespaces, which are declared in DocBlocks
- Supporting collecting string, which looks like a namespace

## Examples

See [`here`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/examples) for graph examples on several levels.

## Configuration

This tool is configurable by a [`YAML`](http://en.wikipedia.org/wiki/YAML) file.
You can copy a prepared file from the vendor directory.

```sh
cp ./vendor/mamuz/php-dependency-analysis/phpda.yml ./myconfig.yml
```

See [`here`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/phpda.yml) for prepared configuration.

### Available Configs

Name             | Type              | Description
---------------- | ----------------- | -----------
*source*         | `string`          | Directory to find files to analyze
*filePattern*    | `string`          | Pattern to match files inside *source* to analyze
*ignore*         | `string`, `array` | Optional: Ignoring directories inside *source*
*formatter*      | `string`          | Output Formatter; must be declared with a [`FQN`](http://en.wikipedia.org/wiki/Fully_qualified_name)
*target*         | `string`          | File path to write output
*visitor*        | `array`           | Optional: Indexed list of visitors to use; each visitor must be declared with a [`FQN`](http://en.wikipedia.org/wiki/Fully_qualified_name)
*visitorOptions* | `array`           | Optional: Associative list modelled by Visitor-FQN => Properties

#### *visitor* Config

FQN                                                  | Description
---------------------------------------------------- | ------------------------------------------
*PhpDA\Parser\Visitor\TagCollector*                  | Collects found namespaces in DocBlocks to declare it as a dependency
*PhpDA\Parser\Visitor\SuperglobalCollector*          | Collects [`PHP-Superglobals`](http://php.net/manual/en/language.variables.superglobals.php) to declare it as a dependency
*PhpDA\Parser\Visitor\UnsupportedEvalCollector*      | Collects `eval` expressions to log it as `Unsupported`
*PhpDA\Parser\Visitor\UnsupportedFuncCollector*      | Collects dynamic function handler, such as `create_function` to log it as `Unsupported`
*PhpDA\Parser\Visitor\UnsupportedVarCollector*       | Collects dynamic variable declarations, such as `$$x` to log it as `Unsupported`
*PhpDA\Parser\Visitor\UnsupportedGlobalCollector*    | Collects `global $foo` expressions to log it as `Unsupported`
*PhpDA\Parser\Visitor\NamespacedStringCollector*     | Collects strings which looks like a namespace to log it as `NamespacedString`
*PhpDA\Parser\Visitor\IocContainerAccessorCollector* | Collects accessor methods which looks like a object retrieval to log it as `NamespacedString`

**NOTICE**
 - Unsupported Collector adds a `§` as Namespace-Prefix to avoid conflicts.
 - NamespacedStrings Collector adds a `?` as Namespace-Prefix to avoid conflicts.

#### *visitorOptions* Config

Each visitor is configurable by setting *visitorOptions*.

Property         | Type      | Description
---------------- | --------- | -----------
*excludePattern* | `string`  | Ignore namespaces where pattern is matched. Default is `null`, which means that filter is disabled
*minDepth*       | `integer` | Ignore namespaces where count of subnamespaces is less than defined. Default is `0`, which means that filter is disabled
*sliceOffset*    | `integer` | Filter namespaces with [`array_slice`](http://php.net/manual/en/function.array-slice.php) on subnamespaces. Default is `null`, which means that filter is disabled
*sliceLength*    | `integer` | Filter namespaces with [`array_slice`](http://php.net/manual/en/function.array-slice.php) on subnamespaces. Default is `null`, which means that filter is disabled

#####

## Usage

Run this command line to create a dependecy graph:

```sh
./vendor/bin/phpda analyze /path/to/myconfig.yml
```

After that open report file, which is declared as `target` in the configuration, with your prefered tool.

## Limitations

PHP is a dynamic language with a weak type system.
It also contains a lot of expressions, which will be resolved first in runtime.
This tool is a static code analysis, thus it have some limitations.
Here is a non-exhaustive list of unsupported php-features:

- Dynamic features such as `eval` and `$$x`
- Globals such as `global $x;`
- Dynamic funcs such as `call_user_func`, `call_user_func_array`, `create_function`

The cleaner your project is, the more dependencies can be detected.
Or in other words, it's highly recommend to have a clean project before using this tool.
Clean means having less violations detected by [`PHP_CodeSniffer`](https://github.com/squizlabs/PHP_CodeSniffer).

## Plugins

### Write your own Visitors

Visitors are provided by Visitor-Pattern implemented by [`Nikic's Php-Parser`](https://github.com/nikic/PHP-Parser).
Read the [`docs`](https://github.com/nikic/PHP-Parser/tree/master/doc) to get into the idea of visitor.

To get your own visitor, just create a new Visitor by extending
[`PhpDA\Parser\Visitor\AbstractVisitor`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/src/Parser/Visitor/AbstractVisitor.php).
Beside this you have to implement one of the follwing Interface to declare the concern:
- `PhpDA\Parser\Visitor\Feature\UsedNamespaceCollectorInterface`
- `PhpDA\Parser\Visitor\Feature\UnsupportedNamespaceCollectorInterface`
- `PhpDA\Parser\Visitor\Feature\NamespacedStringCollectorInterface`

After that you can declare it in the configuration.

### Write your own Formatters

To have an own Formatter to create other Reports, just extend
[`PhpDA\Writer\Strategy\AbstractStrategy`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/src/Writer/Strategy/AbstractStrategy.php)
or implement [`PhpDA\Writer\Strategy\StrategyInterface`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/src/Writer/Strategy/StrategyInterface.php)
and declare it in the configuration.
