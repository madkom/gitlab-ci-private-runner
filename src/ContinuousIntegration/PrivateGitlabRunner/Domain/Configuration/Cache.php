<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

/**
 * Class Cache
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class Cache
{
    /**
     * @var string
     */
    private $cacheKey;
    /**
     * @var string[]
     */
    private $cachePaths;

    /**
     * Cache constructor.
     *
     * @param string   $cacheKey
     * @param string[] $cachePath
     */
    public function __construct($cacheKey, $cachePath)
    {
        $this->cacheKey   = $cacheKey;
        $this->cachePaths = $cachePath;
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->cacheKey;
    }

    /**
     * @return string[]
     */
    public function paths()
    {
        return $this->cachePaths;
    }
}
