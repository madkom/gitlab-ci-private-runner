<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Cache;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfiguration;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Stage;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Variable;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class GitlabCISpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin GitlabCIConfiguration
 */
class GitlabCIConfigurationSpec extends ObjectBehavior
{
    /** @var  Stage[] */
    private $stages;
    /** @var  Cache */
    private $cache;
    /** @var  Variable[] */
    private $variables;
    /** @var  Job[] */
    private $jobs;

    function let(Job $job, Cache $cache, Variable $variable, Stage $stage)
    {
        $job->jobName()->willReturn('phpspec_php_5_6');
        $this->jobs   = [$job];
        $this->stages = [$stage];
        $this->variables = [$variable];
        $this->cache = $cache;

        $this->beConstructedWith($this->stages, $cache, $this->variables, $this->jobs);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GitlabCIConfiguration::class);
    }

    function it_should_values_it_was_constructed_with()
    {
        $this->stages()->shouldReturn($this->stages);
        $this->cache()->shouldReturn($this->cache);
        $this->variables()->shouldReturn($this->variables);
        $this->jobs()->shouldReturn($this->jobs);
    }

    function it_should_throw_exception_if_wrong_type_passed()
    {
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', [[new \stdClass()], $this->cache, $this->variables, $this->jobs]);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', [$this->stages, $this->cache, [new \stdClass()], $this->jobs]);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', [$this->stages, $this->cache, $this->variables, [new \stdClass()]]);
    }

    function it_should_return_if_configuration_contains_specific_job()
    {
        $this->hasJob('phpspec_php_5_6')->shouldReturn(true);
        $this->hasJob('phpspec_php')->shouldReturn(false);
        $this->getJob('phpspec_php_5_6')->shouldReturn($this->jobs[0]);
    }

}

