<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\RunJobCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RunJobCommandSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin RunJobCommand
 */
class RunJobCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\RunJobCommand');
    }
}
