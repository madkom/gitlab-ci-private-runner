<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;


/**
 * Class JobRunner
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class JobRunner
{
    /** Where project will be mapped */
    const CONTAINER_PROJECT = '/build/base';
    /** Where copy the project to run script on it */
    const CONTAINER_PROJECT_COPY = '/build/project';

    /**
     * @var GitlabCIConfigurationFactory
     */
    private $gitlabCIConfigurationFactory;
    /**
     * @var DockerRunCommandBuilder
     */
    private $dockerRunCommandBuilder;
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * JobRunner constructor.
     *
     * @param GitlabCIConfigurationFactory $gitlabCIConfigurationFactory
     * @param DockerRunCommandBuilder      $dockerRunCommandBuilder
     * @param ProcessRunner                $processRunner
     */
    public function __construct(GitlabCIConfigurationFactory $gitlabCIConfigurationFactory, DockerRunCommandBuilder $dockerRunCommandBuilder, ProcessRunner $processRunner)
    {
        $this->gitlabCIConfigurationFactory = $gitlabCIConfigurationFactory;
        $this->dockerRunCommandBuilder      = $dockerRunCommandBuilder;
        $this->processRunner                = $processRunner;
    }

    /**
     * Runs gitlab ci job
     *
     * @param string        $jobName
     * @param string        $gitlabCiPath
     * @param string        $refName
     * @param null|string   $sleep
     * @param array         $volumes
     *
     * @throws PrivateRunnerException
     */
    public function run($jobName, $gitlabCiPath, $refName, $sleep = null, array $volumes = [])
    {
        $dockerRunCommand = $this->buildDockerRunCommand($jobName, $gitlabCiPath, $refName, $sleep, $volumes);

        $this->processRunner->runProcess($dockerRunCommand);
    }

    /**
     * @param string $jobName
     * @param string $gitlabCiPath
     * @param string $refName
     * @param null|string $sleep
     * @param array  $volumes
     *
     * @return string
     * @throws PrivateRunnerException
     */
    private function buildDockerRunCommand($jobName, $gitlabCiPath, $refName, $sleep = null, array $volumes = [])
    {
        $rootDirectory = dirname($gitlabCiPath);
        $projectId     = md5($gitlabCiPath);
        $gitlabConfiguration = $this->gitlabCIConfigurationFactory->createFromYaml($gitlabCiPath);

        if (!file_exists($gitlabCiPath)) {
            throw new PrivateRunnerException("Gitlab configuration doesn't exists on given path {$gitlabCiPath}");
        }
        if (!$gitlabConfiguration->hasJob($jobName)) {
            throw new PrivateRunnerException("Job with given name doesn't exists {$jobName}");
        }
        $job = $gitlabConfiguration->getJob($jobName);


        $dockerRunCommandBuilder = $this->dockerRunCommandBuilder->image($job->imageName());
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->workDir(self::CONTAINER_PROJECT_COPY);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->rm(true);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->entrypoint('/bin/sh');

        foreach ($gitlabConfiguration->variables() as $variable) {
            $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment($variable->key(), $variable->value());
        }
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment('CI_PROJECT_ID', $projectId);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment('CI_PROJECT_DIR', self::CONTAINER_PROJECT_COPY);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment('CI_BUILD_REF_NAME', $refName);

        $dockerRunCommandBuilder = $dockerRunCommandBuilder->volume(self::CONTAINER_PROJECT, $rootDirectory, 'ro');
        foreach ($volumes as $volume) {
            $separatedVolume = explode(':', $volume);
            $hostVolume      = $separatedVolume[0];
            $containerVolume = $separatedVolume[1];
            $dockerRunCommandBuilder = $dockerRunCommandBuilder->volume($hostVolume, $containerVolume);
        }


        $containerProject = self::CONTAINER_PROJECT;
        $containerProjectCopy = self::CONTAINER_PROJECT_COPY;
        $command = "\"-c\" \"cp -R {$containerProject}/. {$containerProjectCopy}/";
        foreach ($job->scripts() as $script) {
            $command .= " && {$script}";
        }
        $command .= $sleep ? " && sleep {$sleep}" : '';
        $command .= "\"";

        $dockerRunCommandBuilder = $dockerRunCommandBuilder->cmd($command);
        return $dockerRunCommandBuilder->toString();
    }

}
