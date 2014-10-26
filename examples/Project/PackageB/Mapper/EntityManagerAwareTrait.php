<?php

namespace PackageB\Mapper;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAwareTrait
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @return object
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return EntityManagerInterface
     * @throws \DomainException
     */
    public function getEntityManager()
    {
        if (!$this->entityManager instanceof EntityManagerInterface) {
            throw new \DomainException('EntityManager has not been set');
        }

        return $this->entityManager;
    }
}
