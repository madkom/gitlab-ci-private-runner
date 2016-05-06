<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Stage;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class GitlabJobSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin Job
 */
class JobSpec extends ObjectBehavior
{
    /** @var  array */
    private $scripts;
    /** @var  array */
    private $except;
    /** @var  array */
    private $only;

    function let()
    {
        $this->scripts = ['phing -f build-dependencies.xml composer-dev'];
        $this->except = ['master'];
        $this->only   = ['tags'];
        $this->beConstructedWith('job_name', 'image_name', new Stage('stage_name'), $this->scripts, $this->except, $this->only);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job');
    }

    function it_should_return_values_it_was_constructed_with()
    {
        $this->jobName()->shouldReturn('job_name');
        $this->imageName()->shouldReturn('image_name');
        $this->stage()->name()->shouldReturn('stage_name');
        $this->scripts()->shouldReturn($this->scripts);
        $this->exceptList()->shouldReturn($this->except);
        $this->onlyList()->shouldReturn($this->only);
    }

    function it_should_throw_exception_if_empty_values_passed()
    {
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['', 'image_name', new Stage('stage'), $this->scripts, $this->except, $this->only]);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['job_name', '', new Stage('stage'), $this->scripts, $this->except, $this->only]);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['job_name', 'image_name', new Stage('stage'), [], $this->except, $this->only]);
    }

}
