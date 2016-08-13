<?php

namespace PackageX;

class AnonymousClass implements \IteratorAggregate
{
    public function getIterator()
    {
        return new class extends \ArrayIterator{};
    }

    public function getRecursiveIterator()
    {
        return new class extends \RecursiveArrayIterator{};
    }

    public function getEmptyIterator()
    {
        return new class {
            public function __invoke() {
                return new class extends \EmptyIterator{};
            }
        };
    }
}
