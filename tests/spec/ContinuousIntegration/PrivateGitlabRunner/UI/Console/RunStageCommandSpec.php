<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\RunStageCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RunStageCommandSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin RunStageCommand
 */
class RunStageCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\RunStageCommand');
    }
}
