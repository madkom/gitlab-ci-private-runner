<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;

/**
 * Class GitlabJob
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class Job
{
    /**
     * @var string
     */
    private $jobName;
    /**
     * @var string
     */
    private $imageName;
    /**
     * @var Stage
     */
    private $stage;
    /**
     * @var string[]
     */
    private $scripts;
    /**
     * @var string[]
     */
    private $exceptList;
    /**
     * @var string[]
     */
    private $onlyList;

    /**
     * Job constructor.
     *
     * @param string $jobName
     * @param string $imageName
     * @param Stage  $stage
     * @param string[] $scripts
     * @param string[] $exceptList
     * @param string[] $onlyList
     */
    public function __construct($jobName, $imageName, Stage $stage, $scripts, $exceptList, $onlyList)
    {
        $this->setJobName($jobName);
        $this->setImageName($imageName);
        $this->setStage($stage);
        $this->setScripts($scripts);
        $this->exceptList = $exceptList;
        $this->onlyList = $onlyList;
    }

    /**
     * @return string
     */
    public function jobName()
    {
        return $this->jobName;
    }

    /**
     * @return string
     */
    public function imageName()
    {
        return $this->imageName;
    }

    /**
     * @return Stage
     */
    public function stage()
    {
        return $this->stage;
    }

    /**
     * @return string[]
     */
    public function scripts()
    {
        return $this->scripts;
    }

    /**
     * @return string[]
     */
    public function exceptList()
    {
        return $this->exceptList;
    }

    /**
     * @return string[]
     */
    public function onlyList()
    {
        return $this->onlyList;
    }

    /**
     * @param string $name
     *
     * @throws PrivateRunnerException
     */
    private function setJobName($name)
    {
        if (!$name) {
            throw new PrivateRunnerException("Job name is empty");
        }

        $this->jobName = $name;
    }

    /**
     * @param string $imageName
     *
     * @throws PrivateRunnerException
     */
    private function setImageName($imageName)
    {
        if (!$imageName) {
            throw new PrivateRunnerException("Image name is empty");
        }

        $this->imageName = $imageName;
    }

    /**
     * @param string $stage
     *
     * @throws PrivateRunnerException
     */
    private function setStage($stage)
    {
        $this->stage = $stage;
    }

    /**
     * @param string $scripts
     *
     * @throws PrivateRunnerException
     */
    private function setScripts($scripts)
    {
        if (!$scripts) {
            throw new PrivateRunnerException("Scripts are empty");
        }

        $this->scripts = $scripts;
    }

}
