<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Symfony\Component\Console\Command\Command;

/**
 * Class BaseCommand
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
abstract class BaseCommand extends Command
{

    /**
     * @return string
     *
     * @throws \Exception
     */
    protected function findGitlabConfig()
    {
        $currentWorkingDir = getcwd();
        $configFileName    = '.gitlab-ci.yml';

        if (file_exists($filePath = $currentWorkingDir . DIRECTORY_SEPARATOR . $configFileName)) {
            return $filePath;
        }
        if (file_exists($filePath = $currentWorkingDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $configFileName)) {
            return $filePath;
        }

        throw new \Exception("Can't find .gitlab-ci.yml. Run the script from root catalog of your application.");
    }

}