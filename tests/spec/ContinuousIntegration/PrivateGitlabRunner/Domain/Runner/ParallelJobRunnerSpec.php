<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfiguration;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Variable;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\ConsoleCommandFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\JobRunner;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\ParallelJobRunner;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\Process;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\ProcessRunner;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ParallelJobRunnerSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin ParallelJobRunner
 */
class ParallelJobRunnerSpec extends ObjectBehavior
{
    /** @var  ProcessRunner */
    private $processRunner;
    /** @var  ConsoleCommandFactory */
    private $consoleCommandFactory;
    /** @var  GitlabCIConfiguration */
    private $gitlabCIConfiguration;
    /** @var  GitlabCIConfigurationFactory */
    private $gitlabCIConfigurationFactory;

    function let(ProcessRunner $processRunner, ConsoleCommandFactory $consoleCommandFactory, GitlabCIConfigurationFactory $gitlabCIConfigurationFactory, GitlabCIConfiguration $gitlabCIConfiguration)
    {
        $this->gitlabCIConfiguration = $gitlabCIConfiguration;
        $this->consoleCommandFactory = $consoleCommandFactory;
        $this->processRunner         = $processRunner;
        $this->gitlabCIConfigurationFactory = $gitlabCIConfigurationFactory;

        $this->beConstructedWith($processRunner, $consoleCommandFactory, $gitlabCIConfigurationFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ParallelJobRunner::class);
    }

    function it_should_manage_all_processes(Job $phpspecJob, Job $phpunitJob, Process $phpspecProcess, Process $phpunitProcess)
    {
        $jobNames       = ['phpspec_php_5_6', 'phpunit_php_5_6'];
        $projectDir     = vfsStream::url('root/project');
        $gitlabYamlPath = $projectDir . '/.gitlab-ci.yml';
        $refName        = 'develop';
        $sleep          = 20;
        $volumes        = ['/artifact_repository:/artifact_repository'];
        $this->gitlabCIConfigurationFactory->createFromYaml($gitlabYamlPath)->willReturn($this->gitlabCIConfiguration);

        $phpspecCommand = 'phpspec some docker run command';
        $phpunitCommand = 'phpunit some docker run command';
        $variables = [ new Variable('some', 'value') ];
        $this->gitlabCIConfiguration->variables()->willReturn($variables);
        $this->gitlabCIConfiguration->getJob('phpspec_php_5_6')->willReturn($phpspecJob);
        $this->gitlabCIConfiguration->getJob('phpunit_php_5_6')->willReturn($phpunitJob);
        $this->consoleCommandFactory->createDockerRunCommand($phpspecJob, $variables, $projectDir, $refName, $sleep, $volumes)->willReturn($phpspecCommand);
        $this->consoleCommandFactory->createDockerRunCommand($phpunitJob, $variables, $projectDir, $refName, $sleep, $volumes)->willReturn($phpunitCommand);
        $this->processRunner->runProcess($phpspecJob, $phpspecCommand)->willReturn($phpspecProcess);
        $this->processRunner->runProcess($phpunitJob, $phpunitCommand)->willReturn($phpunitProcess);


        $phpspecProcess->isRunning()->shouldBeCalledTimes(1)->willReturn(false);
        $phpunitProcess->isRunning()->shouldBeCalledTimes(1)->willReturn(false);

        $phpspecProcess->isSuccessful()->shouldBeCalledTimes(1)->willReturn(true);
        $phpunitProcess->isSuccessful()->shouldBeCalledTimes(1)->willReturn(true);

        $this->runJobs($jobNames, $gitlabYamlPath, $refName, $sleep, $volumes);
    }

    function it_should_throw_exception_if_one_of_the_process_failed(Job $phpspecJob, Job $phpunitJob, Process $phpspecProcess, Process $phpunitProcess)
    {
        $jobNames       = ['phpspec_php_5_6', 'phpunit_php_5_6'];
        $projectDir     = vfsStream::url('root/project');
        $gitlabYamlPath = $projectDir . '/.gitlab-ci.yml';
        $refName        = 'develop';
        $sleep          = 20;
        $volumes        = ['/artifact_repository:/artifact_repository'];
        $this->gitlabCIConfigurationFactory->createFromYaml($gitlabYamlPath)->willReturn($this->gitlabCIConfiguration);

        $phpspecCommand = 'some docker run command';
        $phpunitCommand = 'some docker run command';
        $variables = [ new Variable('some', 'value') ];
        $this->gitlabCIConfiguration->variables()->willReturn($variables);
        $this->gitlabCIConfiguration->getJob('phpspec_php_5_6')->willReturn($phpspecJob);
        $this->gitlabCIConfiguration->getJob('phpunit_php_5_6')->willReturn($phpunitJob);
        $this->consoleCommandFactory->createDockerRunCommand($phpspecJob, $variables, $projectDir, $refName, $sleep, $volumes)->willReturn($phpspecCommand);
        $this->consoleCommandFactory->createDockerRunCommand($phpunitJob, $variables, $projectDir, $refName, $sleep, $volumes)->willReturn($phpunitCommand);
        $this->processRunner->runProcess($phpspecJob, $phpspecCommand)->willReturn($phpspecProcess);
        $this->processRunner->runProcess($phpunitJob, $phpunitCommand)->willReturn($phpunitProcess);

        $phpspecProcess->isRunning()->willReturn(false);
        $phpunitProcess->isRunning()->willReturn(false);

        $phpspecProcess->isSuccessful()->willReturn(true);
        $phpunitProcess->isSuccessful()->willReturn(false);

        $this->shouldThrow(PrivateRunnerException::class)->during('runJobs', [$jobNames, $gitlabYamlPath, $refName, $sleep, $volumes]);
    }

    function it_should_run_stage(Job $phpspecJob, Job $phpunitJob, Process $phpspecProcess, Process $phpunitProcess)
    {
        $stageName      = 'dev-publish-stage';
        $projectDir     = vfsStream::url('root/project');
        $gitlabYamlPath = $projectDir . '/.gitlab-ci.yml';
        $refName        = 'develop';
        $sleep          = 20;
        $volumes        = ['/artifact_repository:/artifact_repository'];
        $this->gitlabCIConfigurationFactory->createFromYaml($gitlabYamlPath)->willReturn($this->gitlabCIConfiguration);
        $this->gitlabCIConfiguration->getJobsForStage('dev-publish-stage')->willReturn(['phpspec_php_5_6', 'phpunit_php_5_6']);

        $phpspecCommand = 'phpspec some docker run command';
        $phpunitCommand = 'phpunit some docker run command';
        $variables = [ new Variable('some', 'value') ];
        $this->gitlabCIConfiguration->variables()->willReturn($variables);
        $this->gitlabCIConfiguration->getJob('phpspec_php_5_6')->willReturn($phpspecJob);
        $this->gitlabCIConfiguration->getJob('phpunit_php_5_6')->willReturn($phpunitJob);
        $this->consoleCommandFactory->createDockerRunCommand($phpspecJob, $variables, $projectDir, $refName, $sleep, $volumes)->willReturn($phpspecCommand);
        $this->consoleCommandFactory->createDockerRunCommand($phpunitJob, $variables, $projectDir, $refName, $sleep, $volumes)->willReturn($phpunitCommand);
        $this->processRunner->runProcess($phpspecJob, $phpspecCommand)->willReturn($phpspecProcess);
        $this->processRunner->runProcess($phpunitJob, $phpunitCommand)->willReturn($phpunitProcess);

        $phpspecProcess->isRunning()->willReturn(false);
        $phpunitProcess->isRunning()->willReturn(false);

        $phpspecProcess->isSuccessful()->shouldBeCalledTimes(1)->willReturn(true);
        $phpunitProcess->isSuccessful()->shouldBeCalledTimes(1)->willReturn(true);

        $this->runStage($stageName, $gitlabYamlPath, $refName, $sleep, $volumes);
    }

}
