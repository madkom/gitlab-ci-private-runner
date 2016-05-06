<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;

/**
 * Class ProcessRunner
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
interface ProcessRunner
{

    /**
     * @param string $processCommand
     * 
     * @return string
     * @throws PrivateRunnerException
     */
    public function runProcess($processCommand);

}
