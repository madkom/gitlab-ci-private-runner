<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;

/**
 * Class GitlabCI
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class GitlabCIConfiguration
{
    /** @var  Stage[] */
    private $stages;
    /** @var  Cache */
    private $cache;
    /** @var  Variable[] */
    private $variables;
    /** @var  Job[] */
    private $jobs;

    /**
     * GitlabCI constructor.
     *
     * @param Stage[]    $stages
     * @param Cache      $cache
     * @param Variable[] $variables
     * @param Job[]      $jobs
     */
    public function __construct(array $stages, Cache $cache, array $variables, array $jobs)
    {
        $this->setStages($stages);
        $this->cache = $cache;
        $this->setVariables($variables);
        $this->setJobs($jobs);
    }

    /**
     * @return array|Stage[]
     */
    public function stages()
    {
        return $this->stages;
    }

    /**
     * @return Cache
     */
    public function cache()
    {
        return $this->cache;
    }

    /**
     * @return array|Variable[]
     */
    public function variables()
    {
        return $this->variables;
    }

    /**
     * @return array|Job[]
     */
    public function jobs()
    {
        return $this->jobs;
    }

    /**
     * @param string $jobName
     *
     * @return Job
     * @throws PrivateRunnerException
     */
    public function getJob($jobName)
    {
        foreach ($this->jobs() as $job) {
            if ($job->jobName() == $jobName) {
                return $job;
            }
        }

        throw new PrivateRunnerException("Job with with name {$jobName} doesn't exists");
    }

    /**
     * @param string $jobName
     *
     * @return bool
     */
    public function hasJob($jobName)
    {
        foreach ($this->jobs() as $job) {
            if ($job->jobName() == $jobName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $stageName
     *
     * @return array
     */
    public function getJobsForStage($stageName)
    {
        $jobNames = [];
        foreach ($this->jobs as $job) {
            if ($job->stage()->name() == $stageName) {
                $jobNames[] = $job->jobName();
            }
        }

        return $jobNames;
    }

    /**
     * @param array $stages
     *
     * @throws PrivateRunnerException
     */
    private function setStages(array $stages)
    {
        foreach ($stages as $stage) {
            if (!($stage instanceof Stage)) {
                throw new PrivateRunnerException("Passed stages should have type of stage");
            }
        }

        $this->stages = $stages;
    }

    /**
     * @param array $variables
     *
     * @throws PrivateRunnerException
     */
    private function setVariables(array $variables)
    {
        foreach($variables as $variable) {
            if (!($variable instanceof Variable)) {
                throw new PrivateRunnerException("Passed variables should have type of variable");
            }
        }

        $this->variables = $variables;
    }

    /**
     * @param array $jobs
     *
     * @throws PrivateRunnerException
     */
    private function setJobs(array $jobs)
    {
        foreach($jobs as $job) {
            if (!($job instanceof Job)) {
                throw new PrivateRunnerException("Passed jobs should have type of job");
            }
        }

        $this->jobs = $jobs;
    }

}
