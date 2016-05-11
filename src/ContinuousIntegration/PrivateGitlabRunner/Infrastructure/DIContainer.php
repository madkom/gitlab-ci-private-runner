<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\JobFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\ConsoleCommandFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\DockerRunCommandBuilder;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\ParallelJobRunner;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service\ProcessRunner;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Parser;

/**
 * Class DIContainer
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class DIContainer
{
    const GITLAB_CONFIGURATION_FACTORY = 'gitlab_configuration_factory';
    const PARALLEL_JOB_RUNNER          = 'parallel_job_runner';

    /** @var Container */
    private $container;

    public function __construct()
    {
        $container = new ContainerBuilder();
        $container
            ->register('process_runner', ProcessRunner::class)
            ->setPublic(false);
        $container
            ->register('job_factory', JobFactory::class)
            ->setPublic(false);
        $container
            ->register('yaml_parser', Parser::class)
            ->setPublic(false);
        $container
            ->register('docker_run_command_builder', DockerRunCommandBuilder::class)
            ->setPublic(false);
        $container
            ->register('console_command_factory', ConsoleCommandFactory::class)
            ->addArgument(new Reference('docker_run_command_builder'))
            ->setPublic(false);
        $container
            ->register('gitlab_configuration_factory', GitlabCIConfigurationFactory::class)
            ->addArgument(new Reference('job_factory'))
            ->addArgument(new Reference('yaml_parser'))
            ->setShared(true);
        $container
            ->register(self::PARALLEL_JOB_RUNNER, ParallelJobRunner::class)
            ->addArgument(new Reference('process_runner'))
            ->addArgument(new Reference('console_command_factory'))
            ->addArgument(new Reference('gitlab_configuration_factory'))
            ->setShared(true);

        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return object
     */
    public function get($name)
    {
        return $this->container->get($name);
    }

    /**
     * @return ParallelJobRunner
     */
    public function getParallelJobRunner()
    {
        return $this->get(self::PARALLEL_JOB_RUNNER);
    }

}
