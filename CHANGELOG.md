# CHANGELOG

## v0.3.4

- Improve color accessabilty
- Increase max_nesting_level for huge projects

## v0.3.3

- Fix in Factory for missing dependency

## v0.3.2

- Fix in CycleDetector

## v0.3.1

- Add ExampleValidator as ReferenceValidator

## v0.3.0

- Change color usage in layout to 8-bit
- Add Cycle Hilighting feature
- Remove edge concentration in GraphViz to improve usability with ReferenceValidator Feature
- Add ReferenceValidator Feature

## v0.2.1

- Add edge concentration in GraphViz
- Fix in NodeNameFilter by return NULL instead of empty array after slicing
- Adapt minor changes of clue/graph dependency
- Fix for collecting dependencies of null filltered ADTs
- Fix in realpath usage for message about write target
- Add Location awareness to Verteces- and Edges-AttributeBag
- Add Location entity
- Fix lost node attributes in NodeName filtering
- Fix lost node attributes in TagCollector

## v0.2.0

- Regenerate examples to new graph layout
- BC in Writer StrategyInterface
- Create GraphBuilder
- Refactor AnalysisCollection
- Add DoctrineCollections
- Change default configuration by adding groupLength config
- Add groupLength configuration to controll grouping feature
- Improve layout
- Add grouping feature to layout
- Change error report accordingly to log levels
- Change aborting to skipping in case of parse error by docBlock name resolver
- Add logger awareness to Analyzer and to NameResolver
- Add injecting logger feature in PluginLoader
- Add ParserLogger
- Add PsrLog

## v0.1.3

- Fix ignoring array keyword of method arguments in NameResolver visitor
- Fix catching phpDocParser exceptions in NameResolver visitor
- Add json writer

## v0.1.0

- Fix detecting in IocContainerAccessorCollector
- Fix providing UsedNamespacing in ADT entity
- Change vertex name in UnsupportedVarCollector to 'dynamic varname'
- Replace prefixes for UnsupportedNamespaces and NamespacedStrings with coloring
- Add Layout with autoswitch for aggregated graph
- Remove detetecting namespaced string for deprecated pear standard in NamespacedStringCollector
- Rename default config file by appending '.dist'
- Add CONTRIBUTING.md
- Change default configuration by adding visitorOptions for MetaNamespaceCollector
- Add Meta object to ADT entity with corresponding collector
- Change default configuration by adding mode config
- Add mode configuration to switch analysis between usage graph (default), call graph and inheritance graph
- Segregate call graph from analysis
- Segregate inheritance graph from analysis
- Add validation on opcode cache configuration for PhpDocBlocks TagCollector
- Refactor Analyze Command and Service Factories

## v0.0.1

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
