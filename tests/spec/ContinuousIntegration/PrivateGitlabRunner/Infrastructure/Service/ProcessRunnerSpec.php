<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service\ProcessRunner;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ProcessRunnerSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Infrastructure\Service
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin ProcessRunner
 */
class ProcessRunnerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Runner\ProcessRunner::class);
    }
}
