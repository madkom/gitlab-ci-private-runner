<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use Symfony\Component\Process\Process;

/**
 * Class ProcessRunner
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class ProcessRunner implements \Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\ProcessRunner
{
    /**
     * @param string $processCommand
     *
     * @return string
     * @throws PrivateRunnerException
     */
    public function runProcess($processCommand)
    {
        $process = new Process($processCommand);
        $process->setTimeout(720);

        $process->run(function ($type, $buffer) {
            if (\Symfony\Component\Process\Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } else {
                echo $buffer;
            }
        });

        if (!$process->isSuccessful()) {
            throw new PrivateRunnerException("Can't process job runner. Problem occurred with handling command: `{$processCommand}`");
        }
    }
}
