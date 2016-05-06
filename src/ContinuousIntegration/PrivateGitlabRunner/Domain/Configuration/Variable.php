<?php

namespace Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

/**
 * Class Variable
 * @package Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class Variable
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $value;

    /**
     * Variable constructor.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function value()
    {
        return $this->value;
    }
}
