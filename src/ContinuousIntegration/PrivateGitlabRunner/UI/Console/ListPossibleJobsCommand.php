<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\DIContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class ListPossibleJobsCommand
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class ListPossibleJobsCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('job:list')
            ->setDescription("List possible jobs to run.\n Example: bin/private-gitlab-runner job:list")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $diContainer     = new DIContainer();
        $gitlabCiYmlPath = $this->findGitlabConfig();
        
        /** @var GitlabCIConfigurationFactory $gitlabConfigurationFactory */
        $gitlabConfigurationFactory = $diContainer->get(DIContainer::GITLAB_CONFIGURATION_FACTORY);
        $gitlabConfiguration = $gitlabConfigurationFactory->createFromYaml($gitlabCiYmlPath);
        
        $jobs = $gitlabConfiguration->jobs();

        $table = new Table($output);
        $table
            ->setHeaders(['name', 'stage', 'image']);
        foreach ($jobs as $job) {
            $table->addRow([$job->jobName(), $job->stage()->name(), $job->imageName()]);
        }

        $table->render();
    }

}
