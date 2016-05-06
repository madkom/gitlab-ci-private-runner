<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\DIContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListPossibleStagesCommand
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class ListPossibleStagesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('private-gitlab-ci:stage:list')
            ->setDescription('List all defined stages.')
            ->addArgument(
                'config_ci',
                InputArgument::REQUIRED,
                'Path to ".gitlab-ci.yml"'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $diContainer     = new DIContainer();
        $gitlabCiYmlPath = $input->getArgument('config_ci');

        /** @var GitlabCIConfigurationFactory $gitlabConfigurationFactory */
        $gitlabConfigurationFactory = $diContainer->get(DIContainer::GITLAB_CONFIGURATION_FACTORY);
        $gitlabConfiguration = $gitlabConfigurationFactory->createFromYaml($gitlabCiYmlPath);

        $stages = $gitlabConfiguration->stages();

        $table = new Table($output);
        $table
            ->setHeaders(['stage']);
        foreach ($stages as $stage) {
            $table->addRow([$stage->name()]);
        }

        $table->render();
    }
}
