PhpDependencyAnalysis
=====================

[![Build Status](https://travis-ci.org/mamuz/PhpDependencyAnalysis.svg?branch=master)](https://travis-ci.org/mamuz/PhpDependencyAnalysis)
[![Coverage Status](https://coveralls.io/repos/mamuz/PhpDependencyAnalysis/badge.png?branch=master)](https://coveralls.io/r/mamuz/PhpDependencyAnalysis?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45/mini.png)](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45)
[![HHVM Status](http://hhvm.h4cc.de/badge/mamuz/php-dependency-analysis.png)](http://hhvm.h4cc.de/package/mamuz/php-dependency-analysis)
[![Dependency Status](https://www.versioneye.com/user/projects/5431680abeeeeed15600019e/badge.svg)](https://www.versioneye.com/user/projects/5431680abeeeeed15600019e)

[![Latest Stable Version](https://poser.pugx.org/mamuz/php-dependency-analysis/v/stable.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Latest Unstable Version](https://poser.pugx.org/mamuz/php-dependency-analysis/v/unstable.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Total Downloads](https://poser.pugx.org/mamuz/php-dependency-analysis/downloads.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![License](https://poser.pugx.org/mamuz/php-dependency-analysis/license.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)

PhpDependencyAnalysis is an extandable static code analysis for
php-projects to provide a dependency graph based on php namespaces.
It builds dependency graphs on customizable levels, e.g. on appliaction-, on package- or on script level.
Thus, it's usable to declare dependencies in general, but it's also usable to
detect dependency violations between layers in a tiered architecture according to
compliance with [`SoC`](http://en.wikipedia.org/wiki/Separation_of_concerns),
[`LoD`](http://en.wikipedia.org/wiki/Law_of_Demeter), [`ADP`](http://en.wikipedia.org/wiki/Acyclic_dependencies_principle) and other
[`Package-Principles`](http://en.wikipedia.org/wiki/Package_principles).
For huge php-projects it's recommend to integrate it to your [`CI`](http://en.wikipedia.org/wiki/Continuous_integration)
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

- Creating dependency graphs on customized levels or on different scopes
- Detect violations between layers in a tiered architecture
- Graphs can be printed in several formats (HTML, SVG, DOT)
- Add your own detection plugins (Visitor)
- Add your own output plugins (Formatter)

## Examples

See [`here`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/examples) for graph examples on several levels.

## Workflow

PhpDependencyAnalysis uses [`Nikic's Php-Parser`](https://github.com/nikic/PHP-Parser) for parsing
php files. It collects all found namespaces adapted from
[`provided visitor pattern`](https://github.com/nikic/PHP-Parser/blob/master/doc/2_Usage_of_basic_components.markdown) to
resolve dependecies to other namespaces, packages or libraries.
After that [`clues's Graph`](https://github.com/clue/graph) is used to illustrate dependencies based
on the [`mathematical graph theory`](http://en.wikipedia.org/wiki/Graph_%28mathematics%29).

## Configuration

This tool is fully configurable by a [`YAML`](http://en.wikipedia.org/wiki/YAML) file.
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
*PhpDA\Parser\Visitor\SuperglobalCollector*          | Collects [`PHP-Superglobals`](http://php.net/manual/en/language.variables.superglobals.php) to declare it as a dependency
*PhpDA\Parser\Visitor\UnsupportedEvalCollector*      | Collects `eval` expressions to log it as unsupported
*PhpDA\Parser\Visitor\UnsupportedFuncCollector*      | Collects dynamic function handler, such as `create_function` to log it as unsupported
*PhpDA\Parser\Visitor\UnsupportedVarCollector*       | Collects dynamic variable declarations, such as `$$x` to log it as unsupported
*PhpDA\Parser\Visitor\UnsupportedGlobalCollector*    | Collects `global $foo` expressions to log it as unsupported
*PhpDA\Parser\Visitor\NamespacedStringCollector*     | Collects strings which looks like a namespace
*PhpDA\Parser\Visitor\IocContainerAccessorCollector* | Collects accessor methods which looks like a object retrieval

#### *visitorOptions* Config

Following built-in visitors are configurable by setting *visitorOptions*.

**PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector**:
**PhpDA\Parser\Visitor\Required\UsedNamespaceCollector**:

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
./vendor/bin/phpda /path/to/myconfig.yml
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
- Non type hinted vars, like `function render($object)` instead of `function render(MyObject $object)`

The cleaner your project is, the more dependencies can be detected.
Or in other words, it's highly recommend to have a clean project before using this tool.
Clean means having not much violations detected by [`PHP_CodeSniffer`](https://github.com/squizlabs/PHP_CodeSniffer)
and [`PHP Mess Detector`](http://phpmd.org/).

## Plugins

### Write your own Visitors

Visitors are provided by Visitor-Pattern implemented by [`Nikic's Php-Parser`](https://github.com/nikic/PHP-Parser).
Read the [`docs`](https://github.com/nikic/PHP-Parser/tree/master/doc) to get into the idea of visitor.

To get your your own visitor, just create a new Visitor by extending
[`PhpDA\Parser\Visitor\AbstractVisitor`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/src/Parser/Visitor/AbstractVisitor.php).
Beside this you can make your visitor configurable by implementing
[`PhpDA\Plugin\ConfigurableInterface`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/src/Plugin/ConfigurableInterface.php)

After that you can declare your own visitor for usage in the configuration.

### Write your own Formatters

To have an own Formatter to create other Reports, just extend
[`PhpDA\Writer\Strategy\AbstractStrategy`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/src/Writer/Strategy/AbstractStrategy.php)
or implement [`PhpDA\Writer\Strategy\StrategyInterface`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/src/Writer/Strategy/StrategyInterface.php)
and declare it in the configuration.
