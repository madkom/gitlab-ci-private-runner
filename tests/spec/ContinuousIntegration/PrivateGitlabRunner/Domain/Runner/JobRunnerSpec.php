<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfiguration;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Stage;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Variable;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\DockerRunCommandBuilder;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\JobRunner;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\ProcessRunner;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class JobRunnerSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin JobRunner
 */
class JobRunnerSpec extends ObjectBehavior
{

    /**
     * @var GitlabCIConfigurationFactory
     */
    private $gitlabCIConfigurationFactory;
    /**
     * @var ProcessRunner
     */
    private $processRunner;
    /**
     * @var DockerRunCommandBuilder
     */
    private $dockerRunCommandBuilder;

    function let(GitlabCIConfigurationFactory $gitlabCIConfigurationFactory, DockerRunCommandBuilder $dockerRunCommandBuilder, ProcessRunner $processRunner)
    {
        $this->gitlabCIConfigurationFactory = $gitlabCIConfigurationFactory;
        $this->dockerRunCommandBuilder      = $dockerRunCommandBuilder;
        $this->processRunner                = $processRunner;

        vfsStream::setup('root', 0775, [
            'project' => [
                '.gitlab-ci.yml' => 'contents'
            ]
        ]);

        $this->beConstructedWith($gitlabCIConfigurationFactory, $dockerRunCommandBuilder, $processRunner);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JobRunner::class);
    }

    function it_should_run_job(GitlabCIConfiguration $gitlabCIConfiguration, DockerRunCommandBuilder $runCommandWithImage, DockerRunCommandBuilder $runCommandWithWorkDir, DockerRunCommandBuilder $runCommandWithRm, DockerRunCommandBuilder $runCommandWithEmptyEntrypoint, DockerRunCommandBuilder $runCommandWithEnvFirst, DockerRunCommandBuilder $runCommandWithEnvSecond, DockerRunCommandBuilder $runCommandWithEnvThird, DockerRunCommandBuilder $runCommandWithEnvFourth, DockerRunCommandBuilder $runCommandWithEnvFifth, DockerRunCommandBuilder $runCommandWithVolume, DockerRunCommandBuilder $runCommandWithCmd)
    {
        $projectDir  = vfsStream::url('root/project');
        $gitlabYamlPath = $projectDir . '/.gitlab-ci.yml';
        $ciProjectId = md5($gitlabYamlPath);
        $jobName = 'phpspec_php_5_6';
        $refName = 'develop';
        $job = new Job($jobName, 'registry.com/php:5.6-cli', new Stage('transfer-changes-phase'), ['phing composer-dev', 'phing phpspec'], [], []);

        $this->gitlabCIConfigurationFactory->createFromYaml($gitlabYamlPath)->willReturn($gitlabCIConfiguration);
        $gitlabCIConfiguration->hasJob($jobName)->willReturn(true);
        $gitlabCIConfiguration->getJob($jobName)->willReturn($job);
        $gitlabCIConfiguration->variables()->willReturn([
            new Variable('CI_CONFIG','some'),
            new Variable('CI_DIRECTORY', '/home')
        ]);


        $this->dockerRunCommandBuilder->image('registry.com/php:5.6-cli')->willReturn($runCommandWithImage);
        $runCommandWithImage->workDir(JobRunner::CONTAINER_PROJECT_COPY)->willReturn($runCommandWithWorkDir);
        $runCommandWithWorkDir->rm(true)->willReturn($runCommandWithRm);
        $runCommandWithRm->entrypoint('/bin/sh')->willReturn($runCommandWithEmptyEntrypoint);

        $runCommandWithEmptyEntrypoint->environment('CI_CONFIG', 'some')->willReturn($runCommandWithEnvFirst);
        $runCommandWithEnvFirst->environment('CI_DIRECTORY', '/home')->willReturn($runCommandWithEnvSecond);
        $runCommandWithEnvSecond->environment('CI_PROJECT_ID', $ciProjectId)->willReturn($runCommandWithEnvThird);
        $runCommandWithEnvThird->environment('CI_PROJECT_DIR', JobRunner::CONTAINER_PROJECT_COPY)->willReturn($runCommandWithEnvFourth);
        $runCommandWithEnvFourth->environment('CI_BUILD_REF_NAME', $refName)->willReturn($runCommandWithEnvFifth);

        $runCommandWithEnvFifth->volume(JobRunner::CONTAINER_PROJECT, $projectDir, 'ro')->willReturn($runCommandWithVolume);

        $runCommandWithVolume->cmd("\"-c\" \"cp -R /build/base/. /build/project/ && phing composer-dev && phing phpspec\"")->willReturn($runCommandWithCmd);
        $command = 'docker run (..) registry.com/php:5.6-cli';
        $runCommandWithCmd->toString()->willReturn($command);

        $this->processRunner->runProcess($command)->shouldBeCalledTimes(1);
        $this->run($jobName, $gitlabYamlPath, $refName);
    }

    function it_should_throw_exception_if_job_doesnt_exists(GitlabCIConfiguration $gitlabCIConfiguration)
    {
        $projectDir  = vfsStream::url('root/project');
        $gitlabYamlPath = $projectDir . '/.gitlab-ci.yml';
        $jobName = 'phpspec_php_5_6';
        $refName = 'develop';

        $this->gitlabCIConfigurationFactory->createFromYaml($gitlabYamlPath)->willReturn($gitlabCIConfiguration);
        $gitlabCIConfiguration->hasJob($jobName)->willReturn(false);

        $this->shouldThrow(PrivateRunnerException::class)->during('run', [$jobName, $gitlabYamlPath, $refName]);
    }

    function it_should_throw_exception_if_file_doesnt_exists()
    {
        $projectDir  = vfsStream::url('root/project');
        $gitlabYamlPath = $projectDir . '/.gitlab-ci2.yml';
        $jobName = 'phpspec_php_5_6';
        $refName = 'develop';

        $this->shouldThrow(PrivateRunnerException::class)->during('run', [$jobName, $gitlabYamlPath, $refName]);
    }

    function it_should_map_volumes_and_sleep_after_performing_an_action(GitlabCIConfiguration $gitlabCIConfiguration, DockerRunCommandBuilder $runCommandWithImage, DockerRunCommandBuilder $runCommandWithWorkDir, DockerRunCommandBuilder $runCommandWithRm, DockerRunCommandBuilder $runCommandWithEmptyEntrypoint, DockerRunCommandBuilder $runCommandWithEnvFirst, DockerRunCommandBuilder $runCommandWithEnvSecond, DockerRunCommandBuilder $runCommandWithEnvThird, DockerRunCommandBuilder $runCommandWithEnvFourth, DockerRunCommandBuilder $runCommandWithEnvFifth, DockerRunCommandBuilder $runCommandWithFirstVolume, DockerRunCommandBuilder $runCommandWithSecondVolume, DockerRunCommandBuilder $runCommandWithThirdVolume, DockerRunCommandBuilder $runCommandWithCmd)
    {
        $projectDir  = vfsStream::url('root/project');
        $gitlabYamlPath = $projectDir . '/.gitlab-ci.yml';
        $ciProjectId = md5($gitlabYamlPath);
        $sleep = 200;
        $mappedVolumes = ['/artifact:/artifact_repository', '/data:/data'];
        $jobName = 'phpspec_php_5_6';
        $refName = 'develop';
        $job = new Job($jobName, 'registry.com/php:5.6-cli', new Stage('transfer-changes-phase'), ['phing composer-dev', 'phing phpspec'], [], []);

        $this->gitlabCIConfigurationFactory->createFromYaml($gitlabYamlPath)->willReturn($gitlabCIConfiguration);
        $gitlabCIConfiguration->hasJob($jobName)->willReturn(true);
        $gitlabCIConfiguration->getJob($jobName)->willReturn($job);
        $gitlabCIConfiguration->variables()->willReturn([
            new Variable('CI_CONFIG','some'),
            new Variable('CI_DIRECTORY', '/home')
        ]);


        $this->dockerRunCommandBuilder->image('registry.com/php:5.6-cli')->willReturn($runCommandWithImage);
        $runCommandWithImage->workDir(JobRunner::CONTAINER_PROJECT_COPY)->willReturn($runCommandWithWorkDir);
        $runCommandWithWorkDir->rm(true)->willReturn($runCommandWithRm);
        $runCommandWithRm->entrypoint('/bin/sh')->willReturn($runCommandWithEmptyEntrypoint);

        $runCommandWithEmptyEntrypoint->environment('CI_CONFIG', 'some')->willReturn($runCommandWithEnvFirst);
        $runCommandWithEnvFirst->environment('CI_DIRECTORY', '/home')->willReturn($runCommandWithEnvSecond);
        $runCommandWithEnvSecond->environment('CI_PROJECT_ID', $ciProjectId)->willReturn($runCommandWithEnvThird);
        $runCommandWithEnvThird->environment('CI_PROJECT_DIR', JobRunner::CONTAINER_PROJECT_COPY)->willReturn($runCommandWithEnvFourth);
        $runCommandWithEnvFourth->environment('CI_BUILD_REF_NAME', $refName)->willReturn($runCommandWithEnvFifth);

        $runCommandWithEnvFifth->volume(JobRunner::CONTAINER_PROJECT, $projectDir, 'ro')->willReturn($runCommandWithFirstVolume);
        $runCommandWithFirstVolume->volume('/artifact', '/artifact_repository')->willReturn($runCommandWithSecondVolume);
        $runCommandWithSecondVolume->volume('/data', '/data')->willReturn($runCommandWithThirdVolume);

        $runCommandWithThirdVolume->cmd("\"-c\" \"cp -R /build/base/. /build/project/ && phing composer-dev && phing phpspec && sleep 200\"")->willReturn($runCommandWithCmd);
        $command = 'docker run (..) registry.com/php:5.6-cli';
        $runCommandWithCmd->toString()->willReturn($command);

        $this->processRunner->runProcess($command)->shouldBeCalledTimes(1);
        $this->run($jobName, $gitlabYamlPath, $refName, $sleep, $mappedVolumes);
    }

}
