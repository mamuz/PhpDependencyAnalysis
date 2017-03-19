<?php

namespace PackageA\Mapper;

class Query implements QueryInterface
{
    use EntityManagerAwareTrait;

    /**
     * @param int $id
     * @return mixed
     */
    public function getPackageById($id)
    {
        return $this->getEntityManager()->findOne($id);
    }

    /** @ param array $criteria */
    public function getPackagesBy(array $criteria)
    {
        return $this->getEntityManager()->findBy($criteria);
    }
}
