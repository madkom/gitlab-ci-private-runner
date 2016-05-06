<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

/**
 * Class Stage
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class Stage
{
    /**
     * @var string
     */
    private $name;

    /**
     * Stage constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param Stage $stage
     *
     * @return bool
     */
    public function equals(Stage $stage)
    {
        return $this->name() == $stage->name();
    }
}
