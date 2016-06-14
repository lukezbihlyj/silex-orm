<?php

namespace LukeZbihlyj\SilexORM;

use Spot\Mapper as SpotMapper;
use Spot\Entity\Collection;

/**
 * @package LukeZbihlyj\SilexORM\Mapper
 */
class Mapper extends SpotMapper implements MapperCacheInterface
{
    const CACHE_MAX_SIZE = 64;

    /**
     * @var array
     */
    public $queryCache = [];

    /**
     * {@inheritDoc}
     */
    public function resolver()
    {
        return new Query\Resolver($this);
    }

    /**
     * @param string $queryHash
     * @return Collection
     */
    public function getQueryFromCache($queryHash)
    {
        if (isset($this->queryCache[$queryHash])) {
            return unserialize($this->queryCache[$queryHash]);
        }

        return false;
    }

    /**
     * @param string $queryHash
     * @param Collection $collection
     * @return void
     */
    public function addQueryToCache($queryHash, Collection $collection)
    {
        if (count($this->queryCache) == self::CACHE_MAX_SIZE) {
            array_shift($this->queryCache);
        }

        $this->queryCache[$queryHash] = serialize($collection);

        return $this;
    }

    /**
     * @return void
     */
    public function clearQueryCache()
    {
        $this->queryCache = [];

        return $this;
    }
}
