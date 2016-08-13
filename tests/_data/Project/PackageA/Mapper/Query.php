<?php

namespace PackageA\Mapper;

class Query implements QueryInterface
{
    use EntityManagerAwareTrait;

    public function getPackageById($id)
    {
        return $this->getEntityManager()->findOne($id);
    }

    public function getPackagesBy(array $criteria)
    {
        return $this->getEntityManager()->findBy($criteria);
    }
}
