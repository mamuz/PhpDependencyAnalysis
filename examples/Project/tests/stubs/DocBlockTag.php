<?php

namespace PackageX;

use PackageA\Entity\Package;
use PackageB\Service\Message as PackageService;
use PackageC\Filter\FilterInterface as Filter;

/**
 * DocBlockTag
 *
 * @Route("/")
 * @property \PackageA\Service\Message $value
 * @property-read PackageService $arg
 * @property-write \Foo\Bar $object
 * @method Constant iterate() borp(array $container, \Closure $callback)
 */
class DocBlockTag
{
    /**
     * @param Filter[] $filter
     * @param bool $bool
     * @return void
     * @throws \DomainException
     */
    public function foo($filter)
    {
        $filter->filter('any');
    }

    /**
     * @return \PackageA\Mapper\Query
     */
    public function baz($filter)
    {
    }

    /**
     * Short Description
     *
     * @deprecated
     * @Entity
     * @Param \MY\Collection $storage
     * @param $adapter
     * @return Integer|$this
     */
    function bar($adapter)
    {
        /** @var Package $fqcn */
        $fqcn = $this->getFoo();
        /** @var \PackageB\Mapper\CommandInterface $adapter */
        return $adapter->create();
    }
}
