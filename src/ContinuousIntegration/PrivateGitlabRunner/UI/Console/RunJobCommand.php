<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\JobRunner;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\DIContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunJobCommand
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class RunJobCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('private-gitlab-ci:job:run')
            ->setDescription('Run gitlab-ci job in docker')
            ->addArgument(
                'config_ci',
                InputArgument::REQUIRED,
                'Path to ".gitlab-ci.yml"'
            )
            ->addArgument(
                'job_name',
                InputArgument::REQUIRED,
                'Name of the job to run'
            )
            ->addArgument(
                'ref_name',
                InputArgument::OPTIONAL,
                'Current git ref name e.g. master, develop, 1.0.1',
                'develop'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $diContainer     = new DIContainer();
        $gitlabCiYmlPath = $input->getArgument('config_ci');
        $jobName         = $input->getArgument('job_name');
        $refName         = $input->getArgument('ref_name');

        /** @var JobRunner $jobRunner */
        $jobRunner = $diContainer->get(DIContainer::JOB_RUNNER);
        $jobRunner->run($jobName, $gitlabCiYmlPath, $refName);
    }
}
