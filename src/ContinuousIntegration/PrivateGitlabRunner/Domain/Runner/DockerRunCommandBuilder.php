<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;

/**
 * Class DockerRunCommandBuilder
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class DockerRunCommandBuilder
{
    /**
     * @var string
     */
    private $imageName;
    /**
     * @var null|array
     */
    private $environments;
    /**
     * @var bool
     */
    private $isAutoRemoved;
    /**
     * @var string
     */
    private $volumeFromContainerName;
    /**
     * @var string[]
     */
    private $volumes;
    /**
     * @var string
     */
    private $containerName;
    /**
     * @var string
     */
    private $commandToRunInContainer;
    /**
     * @var string
     */
    private $entryPoint;
    /**
     * @var string
     */
    private $workingDirectory;

    /**
     * DockerRunCommandBuilder constructor.
     *
     * @param string      $imageName
     * @param string|null $workingDirectory
     * @param string|null $containerName
     * @param bool        $isAutoRemoved
     * @param array       $environments
     * @param string      $volumeFromContainerName
     * @param array       $volumes
     * @param null        $commandToRunInContainer
     * @param null        $entryPoint
     */
    public function __construct($imageName = null, $workingDirectory = null, $containerName = null, $isAutoRemoved = false, array $environments = [], $volumeFromContainerName = null, $volumes = [], $commandToRunInContainer = null, $entryPoint = null)
    {
        $this->imageName     = $imageName;
        $this->workingDirectory = $workingDirectory;
        $this->containerName = $containerName;
        $this->isAutoRemoved = $isAutoRemoved;
        $this->environments  = $environments;
        $this->volumeFromContainerName = $volumeFromContainerName;
        $this->volumes       = $volumes;
        $this->commandToRunInContainer = $commandToRunInContainer;
        $this->entryPoint    = $entryPoint;
    }

    /**
     * @param string $name
     *
     * @return DockerRunCommandBuilder
     */
    public function image($name)
    {
        return new self($name, $this->workingDirectory, $this->containerName, $this->isAutoRemoved, $this->environments, $this->volumeFromContainerName, $this->volumes, $this->commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @param string $envName
     * @param string $envValue
     *
     * @return DockerRunCommandBuilder
     */
    public function environment($envName, $envValue)
    {
        $environments = $this->environments;
        $environments[$envName] = $envValue;

        return new self($this->imageName, $this->workingDirectory, $this->containerName, $this->isAutoRemoved, $environments, $this->volumeFromContainerName, $this->volumes, $this->commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @param bool $isAutoRemoved
     *
     * @return DockerRunCommandBuilder
     */
    public function rm($isAutoRemoved)
    {
        return new self($this->imageName, $this->workingDirectory, $this->containerName, $isAutoRemoved, $this->environments, $this->volumeFromContainerName, $this->volumes, $this->commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @param $volumeFromContainerName
     *
     * @return DockerRunCommandBuilder
     */
    public function volumeFrom($volumeFromContainerName)
    {
        return new self($this->imageName, $this->workingDirectory, $this->containerName, $this->isAutoRemoved, $this->environments, $volumeFromContainerName, $this->volumes, $this->commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @param string      $containerPath
     * @param string|null $hostPath
     * @param string|null $type e.g 'ro' -> read only
     *
     * @return DockerRunCommandBuilder
     */
    public function volume($containerPath, $hostPath = null, $type = null)
    {
        $volumes                 = $this->volumes;
        $volumes[$containerPath] = [
            'path' => $hostPath ? $hostPath : null,
            'type' => $type ? $type : null
        ];
        return new self($this->imageName, $this->workingDirectory, $this->containerName, $this->isAutoRemoved, $this->environments, $this->volumeFromContainerName, $volumes, $this->commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @param string $containerName
     *
     * @return DockerRunCommandBuilder
     */
    public function name($containerName)
    {
        return new self($this->imageName, $this->workingDirectory, $containerName, $this->isAutoRemoved, $this->environments, $this->volumeFromContainerName, $this->volumes, $this->commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @param string $commandToRunInContainer
     *
     * @return DockerRunCommandBuilder
     */
    public function cmd($commandToRunInContainer)
    {
        return new self($this->imageName, $this->workingDirectory, $this->containerName, $this->isAutoRemoved, $this->environments, $this->volumeFromContainerName, $this->volumes, $commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @param string $entryPoint
     *
     * @return DockerRunCommandBuilder
     */
    public function entrypoint($entryPoint)
    {
        return new self($this->imageName, $this->workingDirectory, $this->containerName, $this->isAutoRemoved, $this->environments, $this->volumeFromContainerName, $this->volumes, $this->commandToRunInContainer, $entryPoint);
    }

    /**
     * @param string $workingDirectory
     *
     * @return DockerRunCommandBuilder
     */
    public function workDir($workingDirectory)
    {
        return new self($this->imageName, $workingDirectory, $this->containerName, $this->isAutoRemoved, $this->environments, $this->volumeFromContainerName, $this->volumes, $this->commandToRunInContainer, $this->entryPoint);
    }

    /**
     * @return string
     * @throws PrivateRunnerException
     */
    public function toString()
    {
        if (!$this->imageName) {
            throw new PrivateRunnerException("You need to pass name to create docker run command");
        }

        $dockerRunCommand = 'docker run';
        $dockerRunCommand .= $this->isAutoRemoved ? ' --rm=true' : '';
        $dockerRunCommand .= $this->volumeFromContainerName ? " --volumes-from {$this->volumeFromContainerName}" : '';
        $dockerRunCommand .= $this->containerName ? " --name {$this->containerName}" : '';
        $dockerRunCommand .= $this->entryPoint === '' ? " --entrypoint=''" : ($this->entryPoint ? " --entrypoint={$this->entryPoint}" : '');
        $dockerRunCommand .= $this->workingDirectory ? " -w={$this->workingDirectory}" : '';

        foreach($this->environments as $envName => $envValue) {
            $dockerRunCommand .= " -e {$envName}={$envValue}";
        }

        foreach($this->volumes as $containerPath => $hostPath) {
            $mappingType = $hostPath['type'] ? ":{$hostPath['type']}" : '';
            $hostPath    = $hostPath['path'] ? "{$hostPath['path']}:" : '';

            $dockerRunCommand .= " -v {$hostPath}{$containerPath}{$mappingType}";
        }

        $dockerRunCommand .= " {$this->imageName}";
        $dockerRunCommand .= $this->commandToRunInContainer ? " {$this->commandToRunInContainer}" : '';

        return $dockerRunCommand;
    }
}
