<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\DockerRunCommandBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class DockerRunCommandBuilderSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin DockerRunCommandBuilder
 */
class DockerRunCommandBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\DockerRunCommandBuilder');
    }

    function it_should_return_values_it_was_constructed_with()
    {
        $this->shouldThrow(PrivateRunnerException::class)->during('toString');
    }

    function it_should_create_run_command()
    {
        $commandBuilder = $this
            ->image('ubuntu:14.04');

        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run ubuntu:14.04');
    }

    function it_should_create_run_command_with_environment()
    {
        $commandBuilder = $this
            ->image('ubuntu:14.04')
            ->environment('CI_PROJECT_DIR', '/home/dgafka');

        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run -e CI_PROJECT_DIR=/home/dgafka ubuntu:14.04');

        
        $commandBuilder = $commandBuilder
            ->environment('CI_PROJECT_DIR', '/home/dgafka')
            ->image('ubuntu:14.04');

        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run -e CI_PROJECT_DIR=/home/dgafka ubuntu:14.04');
    }

    function it_should_create_command_with_auto_remove()
    {
        $commandBuilder = $this
            ->image('ubuntu:13.04')
            ->rm(true);
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run --rm=true ubuntu:13.04');


        $commandBuilder = $commandBuilder
            ->rm(false);
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run ubuntu:13.04');
    }

    function it_should_create_command_with_from_volumes()
    {
        $commandBuilder = $this
            ->image('ubuntu:13.04')
            ->volumeFrom('someContainerName');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run --volumes-from someContainerName ubuntu:13.04');

        $commandBuilder = $commandBuilder
            ->volumeFrom(null);
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run ubuntu:13.04');
    }

    function it_should_create_command_with_volumes()
    {
        $commandBuilder = $this
            ->image('ubuntu:13.04')
            ->volume('/containerPath', '/hostPath');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run -v /hostPath:/containerPath ubuntu:13.04');

        $commandBuilder = $commandBuilder
            ->volume('/onlyContainer');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run -v /hostPath:/containerPath -v /onlyContainer ubuntu:13.04');

        $commandBuilder = $commandBuilder
            ->volume('/containerPath');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run -v /containerPath -v /onlyContainer ubuntu:13.04');

        $commandBuilder = $commandBuilder
            ->volume('/containerPath', '/hostPath', 'ro');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run -v /hostPath:/containerPath:ro -v /onlyContainer ubuntu:13.04');
    }

    function it_should_create_command_with_name_for_container()
    {
        $commandBuilder = $this
            ->image('ubuntu:13.04')
            ->name('containerName');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run --name containerName ubuntu:13.04');

        $commandBuilder = $commandBuilder
            ->name(null);
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run ubuntu:13.04');
    }

    function it_should_replace_entry_point()
    {
        $commandBuilder = $this
            ->image('ubuntu:13.04')
            ->entrypoint('/bin/bash');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run --entrypoint=/bin/bash ubuntu:13.04');

        $commandBuilder = $commandBuilder
            ->name(null)
            ->entrypoint('');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run --entrypoint=\'\' ubuntu:13.04');
    }

    function it_should_add_cmd()
    {
        $commandBuilder = $this
            ->image('ubuntu:13.04')
            ->cmd('/bin/bash');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run ubuntu:13.04 /bin/bash');
    }

    function it_should_add_working_dir()
    {
        $commandBuilder = $this
            ->image('ubuntu:13.04')
            ->workDir('/tmp');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run -w=/tmp ubuntu:13.04');

        $commandBuilder = $commandBuilder
            ->name(null)
            ->workDir('');
        $commandBuilder->shouldNotBe($this);
        $commandBuilder->toString()->shouldReturn('docker run ubuntu:13.04');
    }

}
