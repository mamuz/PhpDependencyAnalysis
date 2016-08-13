<?php

namespace PackageA\Mapper;

interface QueryInterface
{
    /**
     * @param int $id
     * @return \PackageA\Entity\Package
     */
    public function getPackageById($id);

    /**
     * @param array $criteria
     * @return \PackageA\Entity\Package[]
     */
    public function getPackagesBy(array $criteria);
}
