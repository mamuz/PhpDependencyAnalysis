<?php

namespace PackageB\Mapper;

interface QueryInterface
{
    /**
     * @param int $id
     * @return \PackageB\Entity\Package
     */
    public function getPackageById($id);

    /**
     * @param array $criteria
     * @return \PackageB\Entity\Package[]
     */
    public function getPackagesBy(array $criteria);
}
