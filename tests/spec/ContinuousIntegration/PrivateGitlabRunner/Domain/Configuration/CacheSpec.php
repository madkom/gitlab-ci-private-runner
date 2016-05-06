<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Cache;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class CacheSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin Cache
 */
class CacheSpec extends ObjectBehavior
{
    /** @var  array */
    private $cachePaths;

    function let()
    {
        $this->cachePaths = ['vendor/', 'bin/'];
        $this->beConstructedWith('$CI_PROJECT_ID', $this->cachePaths);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Cache');
    }

    function it_should_return_values_it_was_constructed_with()
    {
        $this->key()->shouldReturn('$CI_PROJECT_ID');
        $this->paths()->shouldReturn($this->cachePaths);
    }

}
