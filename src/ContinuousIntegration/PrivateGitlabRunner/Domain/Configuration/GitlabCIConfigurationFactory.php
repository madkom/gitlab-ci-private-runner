<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use Symfony\Component\Yaml\Parser;

/**
 * Class GitlabCIConfigurationFactory
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class GitlabCIConfigurationFactory
{
    /**
     * @var JobFactory
     */
    private $jobFactory;
    /**
     * @var Parser
     */
    private $yamlParser;

    /**
     * GitlabCIConfigurationFactory constructor.
     *
     * @param JobFactory $jobFactory
     * @param Parser     $yamlParser
     */
    public function __construct(JobFactory $jobFactory, Parser $yamlParser)
    {
        $this->jobFactory = $jobFactory;
        $this->yamlParser = $yamlParser;
    }

    public function createFromYaml($yamlPath)
    {
        if (!file_exists($yamlPath)) {
            throw new PrivateRunnerException(".gitlab-ci.yml doesn't exists under {$yamlPath} path.");
        }

        $parsedConfiguration = $this->yamlParser->parse(file_get_contents($yamlPath));

        $jobs = [];
        foreach ($parsedConfiguration as $name => $data) {
            if (array_key_exists('image', $data)) {
                $jobs[] = $this->createJob($name, $data);
            }
        }

        return new GitlabCIConfiguration(
            $this->createStages($parsedConfiguration['stages']),
            $this->createCache($parsedConfiguration['cache']),
            $this->createVariables($parsedConfiguration['variables']),
            $jobs
        );
    }

    /**
     * @param array $stageNames
     *
     * @return Stage[]
     */
    private function createStages(array $stageNames)
    {
        $stages = [];
        foreach ($stageNames as $stageName) {
            $stages[] = new Stage($stageName);
        }

        return $stages;
    }

    /**
     * @param array $variableNameAndValues
     *
     * @return Variable[]
     */
    private function createVariables(array $variableNameAndValues)
    {
        $variables = [];
        foreach($variableNameAndValues as $name => $value) {
            $variables[] = new Variable($name, $value);
        }

        return $variables;
    }

    /**
     * @param array $cacheData
     *
     * @return Cache
     */
    private function createCache(array $cacheData)
    {
        return new Cache($cacheData['key'], $cacheData['paths']);
    }

    /**
     * @param $jobName
     * @param $jobData
     *
     * @return Job
     */
    private function createJob($jobName, $jobData)
    {
        return $this->jobFactory->create(
            $jobName,
            $jobData['image'],
            $jobData['stage'],
            $jobData['script'],
            isset($jobData['except']) ? $jobData['except'] : null,
            isset($jobData['only']) ? $jobData['only'] : null
        );
    }

}
