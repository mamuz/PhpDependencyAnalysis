PhpDependencyAnalysis
=====================

**THIS PROJECT IS IN WIP STATE**

PhpDependencyAnalysis is an extandable static code analysis for
php projects to provide a dependency graph based on php namespaces.
It builds dependency graphs on customizable levels, e.g. on framework-, on package- or on script level.
Thus, it's usable to declare dependencies in general, but it's also usable to
detect dependency violations between layers in a layered architecture according to
compliance with SoC, LoD and other Package-Principles.
For huge php-projects it's recommend to integrate it to your CI to monitor dependencies and violations.

[![Build Status](https://travis-ci.org/mamuz/PhpDependencyAnalysis.svg?branch=master)](https://travis-ci.org/mamuz/PhpDependencyAnalysis)
[![Coverage Status](https://coveralls.io/repos/mamuz/PhpDependencyAnalysis/badge.png?branch=master)](https://coveralls.io/r/mamuz/PhpDependencyAnalysis?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45/mini.png)](https://insight.sensiolabs.com/projects/5dad5765-c411-41a5-9d3c-f1cf3d40ed45)
[![HHVM Status](http://hhvm.h4cc.de/badge/mamuz/php-dependency-analysis.png)](http://hhvm.h4cc.de/package/mamuz/php-dependency-analysis)
[![Dependency Status](https://www.versioneye.com/user/projects/5431680abeeeeed15600019e/badge.svg)](https://www.versioneye.com/user/projects/5431680abeeeeed15600019e)

## Installation

The recommended way to install
[`mamuz/php-dependency-analysis`](https://packagist.org/packages/mamuz/php-dependency-analysis) is through
[composer](http://getcomposer.org/) by adding dependency to your `composer.json`:

```json
{
    "require": {
        "mamuz/php-dependency-analysis": "*"
    }
}
```

## Features

- Creating dependency graphs on customized levels
- Detect violations between layers in a layered architecture
- Graphs can be printed in several formats
- Collecting dependencies and detecting violations is extandable

## Workflow

tba

## Configuration

Create a yaml config and handover is to cli invoker.
..tba

## Usage

```sh
phpda analyze ./myconfig.yml
```

## Limitations

tba
