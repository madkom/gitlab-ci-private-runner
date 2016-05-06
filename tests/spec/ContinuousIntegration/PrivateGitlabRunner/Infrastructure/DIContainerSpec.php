<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\JobRunner;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\DIContainer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class DIContainerSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin DIContainer
 */
class DIContainerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\DIContainer');
    }

    function it_should_retrieve_job_runner()
    {
        $this->get(DIContainer::JOB_RUNNER)->shouldHaveType(JobRunner::class);
        $this->get(DIContainer::GITLAB_CONFIGURATION_FACTORY)->shouldHaveType(GitlabCIConfigurationFactory::class);
    }
}
