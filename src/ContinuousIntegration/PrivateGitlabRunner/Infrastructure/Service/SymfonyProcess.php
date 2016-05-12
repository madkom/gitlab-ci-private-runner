<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\Process;

/**
 * Class SymfonyProcess
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class SymfonyProcess implements Process
{
    /**
     * @var Job
     */
    private $job;
    /**
     * @var \Symfony\Component\Process\Process
     */
    private $symfonyProcess;

    /**
     * SymfonyProcess constructor.
     *
     * @param Job                                $job
     * @param \Symfony\Component\Process\Process $symfonyProcess
     */
    public function __construct(Job $job, \Symfony\Component\Process\Process $symfonyProcess)
    {
        $this->job = $job;
        $this->symfonyProcess = $symfonyProcess;
    }

    /**
     * @inheritDoc
     */
    public function isRunning()
    {
        return $this->symfonyProcess->isRunning();
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        if ($this->symfonyProcess->isSuccessful()) {
            echo "\e[33m{$this->job->jobName()}: \e[36mHas finished without errors.";
        }

        return $this->symfonyProcess->isSuccessful();
    }

}