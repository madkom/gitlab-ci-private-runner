<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

/**
 * Interface Process
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
interface Process
{

    /**
     * @return bool
     */
    public function isRunning();

    /**
     * @return bool
     */
    public function isSuccessful();

}