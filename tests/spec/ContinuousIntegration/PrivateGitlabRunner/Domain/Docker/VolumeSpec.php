<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\Volume;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class VolumeSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin Volume
 */
class VolumeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('/data', '/test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Docker\Volume');
    }

    function it_should_return_values_it_was_constructed_with()
    {
        $this->hostVolume()->shouldReturn('/data');
        $this->containerVolume()->shouldReturn('/test');
    }

    function it_should_throw_exception_if_wrong_strings_passed()
    {
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['', '/data']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', [null, '/data']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['dasdasad', '/data']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['blabla/', '/data']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', [1231323, '/data']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['/data', '']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['/data', null]);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['/data', 'dasdasad']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['/data', 'blabla/']);
        $this->shouldThrow(PrivateRunnerException::class)->during('__construct', ['/data', 1231323]);
    }

}
