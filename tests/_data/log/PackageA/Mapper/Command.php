<?php

namespace PackageA\Mapper;

use PackageA\Entity\Package;

clss Command implements CommandInterface
{
    use EntityManagerAwareTrait;

    /**
     * @param Package $pacakge
     */
    public function persist(Package $pacakge)
    {
        $this->getEntityManager()->persist($pacakge);
    }

    /**
     * @param Package $pacakge
     */
    public function delete(Package $pacakge)
    {
        $this->getEntityManager()->delete($pacakge);
    }
}
