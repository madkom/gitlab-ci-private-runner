<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\DIContainer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunStageCommand
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class RunStageCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('stage:run')
            ->setDescription("Run gitlab-ci jobs for specified stage in docker.\n Example: bin/private-gitlab-runner stage:run phpspec_php_5_6 phpunit_php_5_6 --sleep_time=20 --ref_name=test --map_volume=/artificat_repository:/artifact_repository")
            ->addArgument(
                'stage_name',
                InputArgument::REQUIRED,
                'Name of stage to run'
            )
            ->addOption(
                'ref_name',
                null,
                InputArgument::OPTIONAL,
                'Current git ref name e.g. master, develop, 1.0.1',
                'develop'
            )
            ->addOption(
                'sleep_time',
                null,
                InputOption::VALUE_OPTIONAL,
                'For how many second container sleep after performing actions',
                null
            )
            ->addOption(
                'map_volume',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Map extra volumes to the container in format /data:/data /artifact_repository:/artifact',
                []
            );
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $diContainer     = new DIContainer();
        $gitlabCiYmlPath = $this->findGitlabConfig();
        $stageName       = $input->getArgument('stage_name');
        $refName         = $input->getOption('ref_name');
        $sleepTime       = $input->getOption('sleep_time');
        $mappedVolumes   = $input->getOption('map_volume');
        
        $jobRunner = $diContainer->getParallelJobRunner();
        $jobRunner->runStage($stageName, $gitlabCiYmlPath, $refName, $sleepTime, $mappedVolumes);
    }
}
