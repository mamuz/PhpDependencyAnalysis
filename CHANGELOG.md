# CHANGELOG

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
