<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\Process;

/**
 * Class SymfonyProcess
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class SymfonyProcess implements Process
{
    /**
     * @var \Symfony\Component\Process\Process
     */
    private $symfonyProcess;

    /**
     * SymfonyProcess constructor.
     *
     * @param \Symfony\Component\Process\Process $symfonyProcess
     */
    public function __construct(\Symfony\Component\Process\Process $symfonyProcess)
    {
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
        return $this->symfonyProcess->isRunning();
    }

}