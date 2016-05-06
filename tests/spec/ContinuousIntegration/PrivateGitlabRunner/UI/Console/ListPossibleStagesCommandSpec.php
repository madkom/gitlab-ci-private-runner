<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\ListPossibleStagesCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ListPossibleStagesCommandSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin ListPossibleStagesCommand
 */
class ListPossibleStagesCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\ListPossibleStagesCommand');
    }
}
