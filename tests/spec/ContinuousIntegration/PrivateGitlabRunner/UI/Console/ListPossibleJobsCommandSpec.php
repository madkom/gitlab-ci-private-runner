<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\ListPossibleJobsCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ListPossibleJobsCommandSpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin ListPossibleJobsCommand
 */
class ListPossibleJobsCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\UI\Console\ListPossibleJobsCommand');
    }
}
