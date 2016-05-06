<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\JobFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\DockerRunCommandBuilder;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\JobRunner;
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
    const JOB_RUNNER = 'job_runner';

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
            ->register('gitlab_configuration_factory', GitlabCIConfigurationFactory::class)
            ->addArgument(new Reference('job_factory'))
            ->addArgument(new Reference('yaml_parser'))
            ->setShared(true);
        $container
            ->register('job_runner', JobRunner::class)
            ->addArgument(new Reference('gitlab_configuration_factory'))
            ->addArgument(new Reference('docker_run_command_builder'))
            ->addArgument(new Reference('process_runner'))
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
}
