<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;

/**
 * Class Volume
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class Volume
{
    /**
     * @var string
     */
    private $hostVolume;
    /**
     * @var string
     */
    private $containerVolume;

    /**
     * Volume constructor.
     *
     * @param string $hostVolume
     * @param string $containerVolume
     */
    public function __construct($hostVolume, $containerVolume)
    {
        $this->setHostVolume($hostVolume);
        $this->setContainerVolume($containerVolume);
    }

    /**
     * @return string
     */
    public function hostVolume()
    {
        return $this->hostVolume;
    }

    /**
     * @return string
     */
    public function containerVolume()
    {
        return $this->containerVolume;
    }

    /**
     * @param string $hostVolume
     *
     * @throws PrivateRunnerException
     */
    private function setHostVolume($hostVolume)
    {
        if (!$hostVolume || $hostVolume[0] !== '/') {
            throw new PrivateRunnerException("Can't create Volume. Host volume is not correct: `{$hostVolume}`.");
        }

        $this->hostVolume = $hostVolume;
    }

    /**
     * @param string $containerVolume
     *
     * @throws PrivateRunnerException
     */
    private function setContainerVolume($containerVolume)
    {
        if (!$containerVolume || $containerVolume[0] !== '/') {
            throw new PrivateRunnerException("Can't create Volume. Container volume is not correct: `{$containerVolume}`.");
        }

        $this->containerVolume = $containerVolume;
    }

}
