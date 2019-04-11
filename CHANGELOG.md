# Change Log
All notable changes to this project will be documented in this file.

## v2.0.2 - 2019-04-11
### Fixed
- DockerHub Build

## v2.0.1 - 2019-04-11
### Fixed
- Version retrieval

## v2.0.0 - 2019-04-11
### Added
- Docker support
### Changed
- Support only PHP7.3
- Remove phar archive support
- [Lookup for config file in current working directory](https://github.com/mamuz/PhpDependencyAnalysis/pull/35/)
- [@see tag is interpreted wrong](https://github.com/mamuz/PhpDependencyAnalysis/issues/29)
- [Symfony 4?](https://github.com/mamuz/PhpDependencyAnalysis/issues/38)

## v1.3.1 - 2017-03-30
### Fixed
- [phpda not taking relative paths to config file](https://github.com/mamuz/PhpDependencyAnalysis/issues/31)

## v1.3.0 - 2017-03-27
### Fixed
- [Context of warnings is empty in JSON result](https://github.com/mamuz/PhpDependencyAnalysis/pull/26)
- Using cwd in case of default config usage in order to save outputs in working dir
- Add missing level check to errorhandler to support supressing by @
- Duplicated locations entries in metadata of edges are unique now
- Use sort by name in file iteration for having same inspection results on differents hosts

### Changed
- Remove support for PHP5.5
- BC: Exit code for detected violations is 1 instead of 2, 2 is reserved for misconfiguration and other exceptions

### Added
- [Add support to build phar](https://github.com/mamuz/PhpDependencyAnalysis/pull/25)
- [Add a PHAR with selfupdate](https://github.com/mamuz/PhpDependencyAnalysis/pull/23)
- E2E tests with codeception and docker
- Issue template for github
- In order to simplify adding customer plugins: ClassMap configuration for autoloading custom plugins

## v1.2.0 - 2016-02-20
### Added
- Code of Conduct for contributer

### Changed
- [Allow to use relative path in the config yaml](https://github.com/mamuz/PhpDependencyAnalysis/pull/13)

## v1.1.1 - 2016-02-10
### Fixed
- [Bug "Invalid configuration setting: verbose"](https://github.com/mamuz/PhpDependencyAnalysis/issues/14)

## v1.1.0 - 2016-02-07
### Changed
- [Invalid config settings should throw an exception](https://github.com/mamuz/PhpDependencyAnalysis/pull/11)

## v1.0.0 - 2016-02-07
### Changed
- Remove support for running on PHP5.3 and PHP5.4 machines
- [#10: Not conpatible with nikic/php-parser v2.0.0](https://github.com/mamuz/PhpDependencyAnalysis/issues/10)
- Bump docblockParser to new major

## v0.6.1 - 2016-02-03
### Fixed
- [#5: Support Symfony3](https://github.com/mamuz/PhpDependencyAnalysis/issues/5)

## v0.6.0 - 2015-09-13
### Added
- FQCN filter

## v0.5.3 - 2015-07-05
### Changed
- Exit status code is 2 for found violations, 1 for thrown exceptions and 0 for successful execution without violations

### Fixed
- Add missing ErrorHandler
- Remove unneeded OpCodeCache Validator in TagCollector

## v0.5.2 - 2015-06-21
### Changed
- Composer version constraints for graphp dependencies

### Fixed
- Typo in SVG footer

## v0.5.1 - 2015-06-04
### Changed
- [#5: Composer dependency versions](https://github.com/mamuz/PhpDependencyAnalysis/issues/5)

## v0.5.0 - 2015-05-16
### Added
- [#4: Return proper Status Code after running command](https://github.com/mamuz/PhpDependencyAnalysis/issues/4)

## v0.4.2 - 2015-05-10
### Fixed
- Typo for InvalidArgumentException in Config

### Changed
- Update nikic/php-parser to 1.3.*
- Update phpunit/phpunit to 4.6.*

### Added
- Add php7 Anonymous Classes support

## v0.4.1 - 2015-03-28
### Changed
- Update clue/graph to 0.9.*
- Update nikic/php-parser to 1.2.*
- Adapt NamespacedStringCollector and IocContainerAccessorCollector to use new Node\Scalar\String_ object
- Simplify DeclaredNamespaceCollector by using new ClassLike object

### Added
- Add php7 to travis config
- Add php7 Return Type Declarations support
- Add graphp/algorithms 0.8.0

## v0.4.0 - 2015-02-08
### Changed
- Normalize CHANGELOG.md
- Travis configuration
- Optimize .gitignore
- Change composer attribute type to project
- Update phpParser to 1.1.*
- Update graphviz to 0.2.*
- Update phpunit to 4.5.*
- BC: Move GraphViz interface from Builder to Writer
- BC: Refactor and changing data model in Json Writer
- BC: Rename fqcn accessor and mutator from fqn to fqcn in Location entity

### Added
- Add .gitattributes
- Add AbstractGraphViz writer
- Add graph extractor
- BC: Add logEntries mutator to BuilderInterface

### Removed
- BC: Removed fqcn awareness in location entity

## v0.3.4 - 2015-01-21
### Changed
- Improve color accessability
- Increase max_nesting_level for huge projects

## v0.3.3 - 2015-01-17
### Fixed
- Fix in Factory for missing dependency

## v0.3.2 - 2015-01-17
### Fixed
- Fix in CycleDetector

## v0.3.1 - 2015-01-13
### Added
- Add ExampleValidator as ReferenceValidator

## v0.3.0 - 2015-01-12
### Changed
- Change color usage in layout to 8-bit

### Removed
- Remove edge concentration in GraphViz to improve usability with ReferenceValidator Feature

### Added
- Add Cycle Hilighting feature
- Add ReferenceValidator Feature

## v0.2.1 - 2015-01-06
### Fixed
- Fix in NodeNameFilter by return NULL instead of empty array after slicing
- Fix for collecting dependencies of null filltered ADTs
- Fix in realpath usage for message about write target
- Fix lost node attributes in NodeName filtering
- Fix lost node attributes in TagCollector

### Changed
- Adapt minor changes of clue/graph dependency

### Added
- Add edge concentration in GraphViz
- Add Location awareness to Verteces- and Edges-AttributeBag
- Add Location entity

## v0.2.0 - 2014-11-30
### Changed
- Regenerate examples to new graph layout
- BC in Writer StrategyInterface
- Refactor AnalysisCollection
- Change default configuration by adding groupLength config
- Improve layout
- Change error report accordingly to log levels
- Change aborting to skipping in case of parse error by docBlock name resolver

### Added
- Create GraphBuilder
- Add DoctrineCollections
- Add groupLength configuration to controll grouping feature
- Add grouping feature to layout
- Add logger awareness to Analyzer and to NameResolver
- Add injecting logger feature in PluginLoader
- Add ParserLogger
- Add PsrLog

## v0.1.3 - 2014-11-19
### Fixed
- Fix ignoring array keyword of method arguments in NameResolver visitor
- Fix catching phpDocParser exceptions in NameResolver visitor

### Added
- Add json writer

## v0.1.0 - 2014-11-16
### Fixed
- Fix detecting in IocContainerAccessorCollector
- Fix providing UsedNamespacing in ADT entity

### Removed
- Remove detetecting namespaced string for deprecated pear standard in NamespacedStringCollector

### Changed
- Change vertex name in UnsupportedVarCollector to 'dynamic varname'
- Replace prefixes for UnsupportedNamespaces and NamespacedStrings with coloring
- Rename default config file by appending '.dist'
- Change default configuration by adding visitorOptions for MetaNamespaceCollector
- Change default configuration by adding mode config
- Segregate call graph from analysis
- Segregate inheritance graph from analysis
- Refactor Analyze Command and Service Factories

### Added
- Add Layout with autoswitch for aggregated graph
- Add CONTRIBUTING.md
- Add Meta object to ADT entity with corresponding collector
- Add mode configuration to switch analysis between usage graph (default), call graph and inheritance graph
- Add validation on opcode cache configuration for PhpDocBlocks TagCollector

## v0.0.1 - 2014-10-26

### Added
- Add TagCollector for PhpDocBlocks
- Add phpdocumentor/reflection-docblock
- Add ParseError list to output
- Add debug stats to verbosity level
- Provide configFile options overwritable by input args
- Add namespacedStrings awareness
- Add unsupportedStmts awareness
- Add AbstractDataType Traverser
- Add CHANGELOG.md
- Add visitor configuration feature
- Add optional visitors
- Add required visitors
- Add examples
- Add writer
- Add write adapter
- Add parse dispatcher
- Add plugin loader
- Add command
- Add graph library
- Add PHP-Parser
- Add Symfony YAML
- Add Symfony Console
- Add Symfony Finder
- Create skeleton
