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
class ListPossibleStagesCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('stage:list')
            ->setDescription("List all defined stages.\n bin/private-gitlab-runner stage:list")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $diContainer     = new DIContainer();
        $gitlabCiYmlPath = $this->findGitlabConfig();

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
