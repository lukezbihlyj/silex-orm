<?php

namespace LukeZbihlyj\SilexORM;

use Spot\Locator as SpotLocator;

/**
 * @package LukeZbihlyj\SilexORM\Locator
 */
class Locator extends SpotLocator
{
    /**
     * @var array
     */
    protected $mapper = [];

    /**
     * {@inheritDoc}
     */
    public function mapper($entityName)
    {
        if (!isset($this->mapper[$entityName])) {
            $mapper = $entityName::mapper();

            if ($mapper === false) {
                $mapper = 'LukeZbihlyj\SilexORM\Mapper';
            }

            $this->mapper[$entityName] = new $mapper($this, $entityName);
        }

        return $this->mapper[$entityName];
    }
}
