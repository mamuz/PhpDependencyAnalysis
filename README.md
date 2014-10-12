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
php projects to provide a dependency graph based on php namespaces.
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
- Graphs can be printed in several formats (HTML, SVG, DiGraphScript)
- Add your own detection plugins (Visitor)
- Add your own output plugins (Formatter)

## Examples

See [`here`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/examples) for graph examples on several levels.

## Workflow

PhpDependencyAnalysis uses [`Nikic's Php-Parser`](https://github.com/nikic/PHP-Parser/) for parsing
php files. It collects all found namespaces adapted from provided visitor pattern to
resolve dependecies to other packages or libraries.
After that [`clues's Graph`](https://github.com/clue/graph) is used to illustrate dependencies based
on the [`mathematical graph theory`](http://en.wikipedia.org/wiki/Graph_%28mathematics%29).

## Configuration

This tool is fully configurable by a [`YAML`](http://en.wikipedia.org/wiki/YAML) file.
You can copy a prepared file from the vendor directory.

```sh
cp ./vendor/mamuz/php-dependency-analysis/phpda.yml ./myconfig.yml
```

See [`here`](https://github.com/mamuz/PhpDependencyAnalysis/blob/master/phpda.yml) for prepared configuration.

Name             | Type              | Description
---------------- | ----------------- | -----------
*source*         | `string`          | Directory path to analyze
*ignore*         | `string`, `array` | Optional: Ignoring directories inside *source*
*formatter*      | `string`          | Output Formatter; must be declared with a [`FQN`](http://en.wikipedia.org/wiki/Fully_qualified_name)
*target*         | `string`          | File path to create output
*visitor*        | `array`           | Optional: Indexed list of visitors to use; each visitor must be declared with a [`FQN`](http://en.wikipedia.org/wiki/Fully_qualified_name)
*visitorOptions* | `array`           | Optional: Associative list modelled by Visitor-FQN => Properties

## Usage

Run this command line to create a dependecy graph:

```sh
./vendor/bin/phpda analyze ./myconfig.yml
```

After that open report file, which is declared as `target` in the configuration, with your prefered tool.

## Limitations

PHP is a dynamic language with a weak type system.
It also contains a lot of expressions, which will be resolved first in runtime.
This tool is a static code analysis, thus it have some limitations.
Here is a non-exhaustive list:

- Dynamic features such as `eval` and `$$x`
- Globals such as `global $x;`
- Dynamic funcs such as `call_user_func`, `call_user_func_array`, `create_function`
- Non type hinted vars, like `function render($object)` instead of `function render(MyObject $object)`

The cleaner your project is, the more dependencies are detected.
Or in other words, it's highly recommend to have a clean project before using this tool.
Clean means having not much violations detected by [`PHP_CodeSniffer`](https://github.com/squizlabs/PHP_CodeSniffer)
and [`PHP Mass Detector`](http://phpmd.org/).

## Plugins

### Write your own Visitors

### Write your own Formatters
