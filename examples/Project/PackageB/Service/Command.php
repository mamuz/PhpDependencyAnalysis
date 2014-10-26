<?php

namespace PackageB\Service;

use PackageB\Entity\Package;
use PackageB\Mapper\CommandInterface;

class Command extends AbstractService
{
    /** @var CommandInterface */
    private $mapper;

    /**
     * @param CommandInterface $mapper
     */
    public function __construct(CommandInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param array $data
     * @return void
     */
    public function persist(array $data)
    {
        $data = array_map('trim', $data);
        $this->mapper->persist(new Package($data));
    }

    /**
     * @param integer $id
     * @return void
     */
    public function deleteAction($id)
    {
        $this->mapper->delete(intval($id));
    }
}
