PhpDependencyAnalysis
=====================

**THIS PROJECT IS IN WIP STATE, NO RELEASE EXISTS YET**

PhpDependencyAnalysis is an extandable static code analysis for
php projects to provide a dependency graph based on php namespaces.
It builds dependency graphs on customizable levels, e.g. on framework-, on package- or on script level.
Thus, it's usable to declare dependencies in general, but it's also usable to
detect violations between layers in a layered architecture, like SoC, LoD and other Package-Principles.
For huge php-projects it's recommend to integrate this to the CI process to monitor dependencies and package violations.

[![Build Status](https://travis-ci.org/mamuz/PhpDependencyAnalysis.svg?branch=master)](https://travis-ci.org/mamuz/PhpDependencyAnalysis)
[![Coverage Status](https://coveralls.io/repos/mamuz/PhpDependencyAnalysis/badge.png?branch=master)](https://coveralls.io/r/mamuz/PhpDependencyAnalysis?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mamuz/PhpDependencyAnalysis/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8ed31e07-75b3-462c-a6ca-fce63b401eb8/mini.png)](https://insight.sensiolabs.com/projects/8ed31e07-75b3-462c-a6ca-fce63b401eb8)
[![HHVM Status](http://hhvm.h4cc.de/badge/mamuz/php-dependency-analysis.png)](http://hhvm.h4cc.de/package/mamuz/php-dependency-analysis)
[![Dependency Status](https://www.versioneye.com/user/projects/538f788746c473980c00001d/badge.svg)](https://www.versioneye.com/user/projects/538f788746c473980c00001d)

[![Latest Stable Version](https://poser.pugx.org/mamuz/php-dependency-analysis/v/stable.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Latest Unstable Version](https://poser.pugx.org/mamuz/php-dependency-analysis/v/unstable.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![Total Downloads](https://poser.pugx.org/mamuz/php-dependency-analysis/downloads.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)
[![License](https://poser.pugx.org/mamuz/php-dependency-analysis/license.svg)](https://packagist.org/packages/mamuz/php-dependency-analysis)

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

## Configuration

Create a yaml config and handover is to cli invoker.
..tba

## Workflow

tba

## Usage

```sh
phpda analyze ./myconfig.yml
```
