<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;

/**
 * Class ProcessRunner
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
interface ProcessRunner
{

    /**
     * @param Job    $jobName
     * @param string $processCommand
     *
     * @return Process
     */
    public function runProcess(Job $jobName, $processCommand);

}
