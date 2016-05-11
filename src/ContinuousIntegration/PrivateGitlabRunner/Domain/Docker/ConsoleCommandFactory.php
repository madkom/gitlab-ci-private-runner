<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Variable;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;

/**
 * Class ConsoleCommandFactory
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class ConsoleCommandFactory
{
    /** Where project will be mapped */
    const CONTAINER_PROJECT = '/build/base';
    /** Where copy the project to run script on it */
    const CONTAINER_PROJECT_COPY = '/build/project';

    /**
     * @var DockerRunCommandBuilder
     */
    private $dockerRunCommandBuilder;

    /**
     * ConsoleCommandFactory constructor.
     *
     * @param DockerRunCommandBuilder $dockerRunCommandBuilder
     */
    public function __construct(DockerRunCommandBuilder $dockerRunCommandBuilder)
    {
        $this->dockerRunCommandBuilder = $dockerRunCommandBuilder;
    }

    /**
     * @param Job                   $job
     * @param Variable[]            $variables
     * @param string                $projectRootPath
     * @param string|null           $gitRefName
     * @param int|null              $sleep
     * @param string[]|null         $volumes
     *
     * @return string
     */
    public function createDockerRunCommand(Job $job, array $variables, $projectRootPath, $gitRefName = null, $sleep = null, $volumes = null)
    {
        $projectId  = md5($projectRootPath);
        $gitRefName = $gitRefName ? $gitRefName : 'develop';
        $volumes    = $volumes ? $this->getVolumesFromString($volumes) : [];

        $dockerRunCommandBuilder = $this->dockerRunCommandBuilder->image($job->imageName());
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->workDir(self::CONTAINER_PROJECT_COPY);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->rm(true);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->entrypoint('/bin/sh');

        foreach ($variables as $variable) {
            $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment($variable->key(), $variable->value());
        }
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment('CI_PROJECT_ID', $projectId);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment('CI_PROJECT_DIR', self::CONTAINER_PROJECT_COPY);
        $dockerRunCommandBuilder = $dockerRunCommandBuilder->environment('CI_BUILD_REF_NAME', $gitRefName);

        $dockerRunCommandBuilder = $dockerRunCommandBuilder->volume(self::CONTAINER_PROJECT, $projectRootPath, 'ro');
        foreach ($volumes as $volume) {
            $dockerRunCommandBuilder = $dockerRunCommandBuilder->volume($volume->hostVolume(), $volume->containerVolume());
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

    /**
     * @param string[] $volumesAsString
     *
     * @return Volume[]
     * @throws PrivateRunnerException
     */
    private function getVolumesFromString($volumesAsString)
    {
        $volumes = [];

        if (!is_array($volumesAsString)) {
            throw new PrivateRunnerException("Passed wrong mapping. Should be /data:/data");
        }

        foreach ($volumesAsString as $volumeAsString) {
            if (!is_string($volumeAsString) || !$volumeAsString) {
                throw new PrivateRunnerException("Passed wrong mapping. Should be /data:/data");
            }

            $mappedVolume = explode(":", $volumeAsString);
            if (count($mappedVolume) != 2) {
                throw new PrivateRunnerException("Passed wrong mapping: {$volumeAsString} should be /data:/data");
            }

            $volumes[] = new Volume($mappedVolume[0], $mappedVolume[1]);
        }

        return $volumes;
    }

}
