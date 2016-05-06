<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Variable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class VariableSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin Variable
 */
class VariableSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith('key', 'value');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Variable');
    }

    function it_should_return_values_it_was_constructed_with()
    {
        $this->key()->shouldReturn('key');
        $this->value()->shouldReturn('value');
    }

}
