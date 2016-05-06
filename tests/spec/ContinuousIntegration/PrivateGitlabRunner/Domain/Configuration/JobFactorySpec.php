<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\JobFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class JobFactorySpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin JobFactory
 */
class JobFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\JobFactory');
    }

    function it_should_create_job()
    {
        $scripts = ['phing -f build-dependencies.xml composer-dev'];
        $except = ['master'];
        $only   = ['tags'];

        $job = $this->create('job_name', 'image_name', 'stage', $scripts, $except, $only);

        $job->jobName()->shouldReturn('job_name');
        $job->imageName()->shouldReturn('image_name');
        $job->stage()->name()->shouldReturn('stage');
        $job->scripts()->shouldReturn($scripts);
        $job->exceptList()->shouldReturn($except);
        $job->onlyList()->shouldReturn($only);
    }

    function it_should_create_with_default_values_if_null_passed()
    {
        $scripts = ['phing -f build-dependencies.xml composer-dev'];
        $except = null;
        $only   = null;

        $job = $this->create('job_name', 'image_name', 'stage', $scripts, $except, $only);

        $job->jobName()->shouldReturn('job_name');
        $job->imageName()->shouldReturn('image_name');
        $job->stage()->name()->shouldReturn('stage');
        $job->scripts()->shouldReturn($scripts);
        $job->exceptList()->shouldReturn([]);
        $job->onlyList()->shouldReturn([]);
    }

}
