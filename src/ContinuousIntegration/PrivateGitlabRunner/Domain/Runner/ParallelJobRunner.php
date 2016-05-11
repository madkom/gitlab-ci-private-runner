<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\ConsoleCommandFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;

/**
 * Class ParallelJobRunner
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class ParallelJobRunner
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;
    /**
     * @var GitlabCIConfigurationFactory
     */
    private $gitlabCIConfigurationFactory;
    /**
     * @var ConsoleCommandFactory
     */
    private $consoleCommandFactory;

    /**
     * Bla constructor.
     *
     * @param ProcessRunner         $processRunner
     * @param ConsoleCommandFactory $consoleCommandFactory
     * @param GitlabCIConfigurationFactory $gitlabCIConfigurationFactory
     */
    public function __construct(ProcessRunner $processRunner, ConsoleCommandFactory $consoleCommandFactory, GitlabCIConfigurationFactory $gitlabCIConfigurationFactory)
    {
        $this->processRunner                = $processRunner;
        $this->consoleCommandFactory        = $consoleCommandFactory;
        $this->gitlabCIConfigurationFactory = $gitlabCIConfigurationFactory;
    }

    /**
     * Run passed jobs
     *
     * @param string[]  $jobNames
     * @param string    $gitlabCiPath
     * @param string    $refName
     * @param null|int  $sleep
     * @param array     $volumes
     *
     * @throws PrivateRunnerException
     */
    public function runJobs(array $jobNames, $gitlabCiPath, $refName, $sleep = null, array $volumes = [])
    {
        /** @var Process[] $runningProcesses */
        $runningProcesses = [];
        $projectRootPath  = dirname($gitlabCiPath);
        $gitlabCIConfiguration = $this->gitlabCIConfigurationFactory->createFromYaml($gitlabCiPath);

        foreach ($jobNames as $jobName) {
            $job     = $gitlabCIConfiguration->getJob($jobName);
            $command = $this->consoleCommandFactory->createDockerRunCommand(
                $job, $gitlabCIConfiguration->variables(), $projectRootPath, $refName, $sleep, $volumes
            );

            $process = $this->processRunner->runProcess($job, $command);
            $runningProcesses[$jobName] = $process;
        }

        $errorProcesses = [];
        foreach ($runningProcesses as $jobName => $process) {
            while ($process->isRunning()) {}

            if (!$process->isSuccessful()) {
                $errorProcesses[] = $jobName;
            }
        }

        if ($errorProcesses) {
            $jobNamesToString = implode(", ", $errorProcesses);
            throw new PrivateRunnerException("Failed jobs: {$jobNamesToString}");
        }
    }

    /**
     * Run all jobs in stage
     *
     * @param string    $stageName
     * @param string    $gitlabCiPath
     * @param string    $refName
     * @param null|int  $sleep
     * @param array     $volumes
     *
     * @throws PrivateRunnerException
     */
    public function runStage($stageName, $gitlabCiPath, $refName, $sleep = null, array $volumes = [])
    {
        $gitlabCIConfiguration = $this->gitlabCIConfigurationFactory->createFromYaml($gitlabCiPath);
        $jobNames = $gitlabCIConfiguration->getJobsForStage($stageName);
        
        $this->runJobs($jobNames, $gitlabCiPath, $refName, $sleep, $volumes);
    }
}
