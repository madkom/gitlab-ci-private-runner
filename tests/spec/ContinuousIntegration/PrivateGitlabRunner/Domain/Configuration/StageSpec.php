<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Stage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class StageSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin Stage
 */
class StageSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith('stageName');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Stage');
    }

    function it_should_return_name()
    {
        $this->name()->shouldReturn('stageName');
    }

    function it_should_for_equality()
    {
        $this->equals(new Stage('stageName'))->shouldReturn(true);
        $this->equals(new Stage('someOther'))->shouldReturn(false);
    }

}
