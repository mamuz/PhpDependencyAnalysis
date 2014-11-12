# Contributing

Thank you for contributing!

Here are some guidelines that you need to follow.
These guidelines exist to keep the code base clean.

## Workflow

1. Fork the project
2. Create a local development Branch for the changes.
3. Commit a change and push your local branch to your github fork.
4. Send a pull-request for your changes to `master`.

## Coding Standard

Use PSR-2:

* https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

Use PHPMD with default ruleset:

* http://phpmd.org/rules/index.html

## Unit-Tests

Add a test for your pull-request.

You can run the unit-tests by calling `phpunit` from the root of the project.

## Travis

Your pull-request will run through [Travis CI](http://www.travis-ci.org)

If you break the tests, your code wont be merged,
so make sure that your code is working before opening up a pull-request.

## Code Review

Your pull-request will be under code review.
So be sure that your code is (nearly) [SOLID and not STUPID](http://williamdurand.fr/2013/07/30/from-stupid-to-solid-code/).
