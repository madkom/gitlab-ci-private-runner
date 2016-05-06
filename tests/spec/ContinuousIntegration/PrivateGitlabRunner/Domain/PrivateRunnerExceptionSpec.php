<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PrivateRunnerExceptionSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin PrivateRunnerException
 */
class PrivateRunnerExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Exception::class);
    }
}
