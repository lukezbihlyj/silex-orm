<?php

namespace LukeZbihlyj\SilexORM;

use Spot\Entity\Collection;

/**
 * @package LukeZbihlyj\SilexORM\MapperCacheInterface
 */
interface MapperCacheInterface
{
    /**
     * @param string $queryHash
     * @return Collection
     */
    public function getQueryFromCache($queryHash);

    /**
     * @param string $queryHash
     * @param Collection $collection
     * @return void
     */
    public function addQueryToCache($queryHash, Collection $collection);

    /**
     * @return void
     */
    public function clearQueryCache();
}
