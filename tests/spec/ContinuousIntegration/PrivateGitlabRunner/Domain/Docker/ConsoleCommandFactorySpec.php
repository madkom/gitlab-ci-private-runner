<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Stage;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Variable;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\ConsoleCommandFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\DockerRunCommandBuilder;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ConsoleCommandFactorySpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin ConsoleCommandFactory
 */
class ConsoleCommandFactorySpec extends ObjectBehavior
{
    /** @var  DockerRunCommandBuilder */
    private $dockerRunCommandBuilder;

    function let(DockerRunCommandBuilder $dockerRunCommandBuilder)
    {
        $this->dockerRunCommandBuilder = $dockerRunCommandBuilder;
        $this->beConstructedWith($dockerRunCommandBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\ConsoleCommandFactory');
    }

    function it_should_create_console_command(DockerRunCommandBuilder $runCommandWithImage, DockerRunCommandBuilder $runCommandWithWorkDir, DockerRunCommandBuilder $runCommandWithRm, DockerRunCommandBuilder $runCommandWithEmptyEntrypoint, DockerRunCommandBuilder $runCommandWithEnvFirst, DockerRunCommandBuilder $runCommandWithEnvSecond, DockerRunCommandBuilder $runCommandWithEnvThird, DockerRunCommandBuilder $runCommandWithEnvFourth, DockerRunCommandBuilder $runCommandWithEnvFifth, DockerRunCommandBuilder $runCommandWithFirstVolume, DockerRunCommandBuilder $runCommandWithSecondVolume, DockerRunCommandBuilder $runCommandWithThirdVolume, DockerRunCommandBuilder $runCommandWithCmd)
    {
        $job = new Job('phpspec_php_5_6', 'registry.com/php:5.6-cli', new Stage('transfer-changes-phase'), ['phing composer-dev', 'phing phpspec'], [], []);
        $refName = 'develop';
        $sleep = 200;
        $mappedVolumes = ['/artifact:/artifact_repository', '/data:/data'];
        $projectRoot = vfsStream::url('root/project');
        $ciProjectId = md5($projectRoot);
        $variables = [new Variable('CI_CONFIG','some'), new Variable('CI_DIRECTORY', '/home')];

        $this->dockerRunCommandBuilder->image('registry.com/php:5.6-cli')->willReturn($runCommandWithImage);
        $runCommandWithImage->workDir(ConsoleCommandFactory::CONTAINER_PROJECT_COPY)->willReturn($runCommandWithWorkDir);
        $runCommandWithWorkDir->rm(true)->willReturn($runCommandWithRm);
        $runCommandWithRm->entrypoint('/bin/sh')->willReturn($runCommandWithEmptyEntrypoint);

        $runCommandWithEmptyEntrypoint->environment('CI_CONFIG', 'some')->willReturn($runCommandWithEnvFirst);
        $runCommandWithEnvFirst->environment('CI_DIRECTORY', '/home')->willReturn($runCommandWithEnvSecond);
        $runCommandWithEnvSecond->environment('CI_PROJECT_ID', $ciProjectId)->willReturn($runCommandWithEnvThird);
        $runCommandWithEnvThird->environment('CI_PROJECT_DIR', ConsoleCommandFactory::CONTAINER_PROJECT_COPY)->willReturn($runCommandWithEnvFourth);
        $runCommandWithEnvFourth->environment('CI_BUILD_REF_NAME', $refName)->willReturn($runCommandWithEnvFifth);

        $runCommandWithEnvFifth->volume(ConsoleCommandFactory::CONTAINER_PROJECT, $projectRoot, 'ro')->willReturn($runCommandWithFirstVolume);
        $runCommandWithFirstVolume->volume('/artifact', '/artifact_repository')->willReturn($runCommandWithSecondVolume);
        $runCommandWithSecondVolume->volume('/data', '/data')->willReturn($runCommandWithThirdVolume);

        $runCommandWithThirdVolume->cmd("\"-c\" \"cp -R /build/base/. /build/project/ && phing composer-dev && phing phpspec && sleep 200\"")->willReturn($runCommandWithCmd);
        $command = 'docker run (..) registry.com/php:5.6-cli';
        $runCommandWithCmd->toString()->willReturn($command);

        $this->createDockerRunCommand($job, $variables, $projectRoot, $refName, $sleep, $mappedVolumes)->shouldReturn($command);
    }

    function it_should_throw_exception_when_wrong_volumes_passed()
    {
        $job = new Job('phpspec_php_5_6', 'registry.com/php:5.6-cli', new Stage('transfer-changes-phase'), ['phing composer-dev', 'phing phpspec'], [], []);
        $refName = 'develop';
        $sleep = 200;
        $projectRoot = vfsStream::url('root/project');
        $variables = [new Variable('CI_CONFIG','some'), new Variable('CI_DIRECTORY', '/home')];

        $this->shouldThrow(PrivateRunnerException::class)->during('createDockerRunCommand', [$job, $variables, $projectRoot, $refName, $sleep, ['/dsa:/dsadas:/dsadasd']]);
        $this->shouldThrow(PrivateRunnerException::class)->during('createDockerRunCommand', [$job, $variables, $projectRoot, $refName, $sleep, [[]]]);
        $this->shouldThrow(PrivateRunnerException::class)->during('createDockerRunCommand', [$job, $variables, $projectRoot, $refName, $sleep, [null]]);
    }

}
