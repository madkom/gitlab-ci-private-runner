<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

/**
 * Class JobFactory
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class JobFactory
{

    /**
     * Job constructor.
     *
     * @param string   $jobName
     * @param string   $imageName
     * @param string   $stageName
     * @param string[] $scripts
     * @param string[] $exceptList
     * @param string[] $onlyList
     *
     * @return Job
     */
    public function create($jobName, $imageName, $stageName, $scripts, $exceptList, $onlyList)
    {
        return new Job(
            $jobName,
            $imageName,
            new Stage($stageName),
            $scripts,
            $exceptList ? $exceptList : [],
            $onlyList ? $onlyList : []
        );
    }
}
