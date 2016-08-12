<?php

namespace PackageA\Service;

use PackageA\Mapper\QueryInterface;

class Query extends AbstractService
{
    /** @var QueryInterface */
    private $mapper;

    /**
     * @param QueryInterface $mapper
     */
    public function __construct(QueryInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param array $criteria
     * @return \PackageA\Entity\Package|\PackageA\Entity\Package[]
     */
    public function find(array $criteria)
    {
        if (array_key_exists('id', $criteria)) {
            return $this->mapper->getPackageById($criteria['id']);
        }

        return $this->mapper->getPackagesBy($criteria);
    }
}
