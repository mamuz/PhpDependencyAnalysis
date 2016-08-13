<?php

namespace PackageB\Mapper;

use PackageB\Entity\Package;

class Command implements CommandInterface
{
    use EntityManagerAwareTrait;

    public function persist(Package $pacakge)
    {
        $this->getEntityManager()->persist($pacakge);
    }

    public function delete(Package $pacakge)
    {
        $this->getEntityManager()->delete($pacakge);
    }
}
