<?php

namespace PackageB\Mapper;

use PackageB\Entity\Package;

interface CommandInterface
{
    /**
     * @param Package $package
     */
    public function persist(Package $package);

    /**
     * @param Package $package
     */
    public function delete(Package $package);
}
