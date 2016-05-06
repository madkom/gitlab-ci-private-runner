<?php

namespace spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration;

use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfiguration;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\Job;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\JobFactory;
use Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\PrivateRunnerException;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Yaml\Parser;

/**
 * Class GitlabCIConfigurationFactorySpec
 * @package spec\Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin GitlabCIConfigurationFactory
 */
class GitlabCIConfigurationFactorySpec extends ObjectBehavior
{

    function let()
    {
        $jobFactory = new JobFactory();
        $yamlParser = new Parser();
        
        $this->beConstructedWith($jobFactory, $yamlParser);
        
        vfsStream::setup('root', 0775, [
            'project' => [
                'gitlab.ci.yml' => $this->getGitlabConfiguration()     
            ]
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\ContinuousIntegration\PrivateGitlabRunner\Domain\Configuration\GitlabCIConfigurationFactory');
    }
    
    function it_should_create_from_yaml_file()
    {
        /** @var GitlabCIConfiguration $gitlabCIConfiguration */
        $gitlabCIConfiguration = $this->createFromYaml(vfsStream::url('root/project/gitlab.ci.yml'));
        $gitlabCIConfiguration->shouldHaveType($gitlabCIConfiguration);
        
        $gitlabCIConfiguration->cache()->key()->shouldReturn('$CI_PROJECT_ID');
        $gitlabCIConfiguration->cache()->paths()->shouldReturn(['vendor/', 'bin/']);
        
        $gitlabCIConfiguration->stages()->shouldHaveCount(4);
        $gitlabCIConfiguration->stages()[0]->name()->shouldReturn('transfer-changes-phase');
        $gitlabCIConfiguration->stages()[1]->name()->shouldReturn('functional-tests-phase');
        $gitlabCIConfiguration->stages()[2]->name()->shouldReturn('dev-publish-phase');
        $gitlabCIConfiguration->stages()[3]->name()->shouldReturn('artifact-clean');
        
        $gitlabCIConfiguration->variables()->shouldHaveCount(1);
        $gitlabCIConfiguration->variables()[0]->key()->shouldReturn('COMPOSER_CACHE_DIR');
        $gitlabCIConfiguration->variables()[0]->value()->shouldReturn('/cache/composer');

        $gitlabCIConfiguration->jobs()->shouldHaveCount(6);

        $gitlabCIConfiguration->jobs()[0]->shouldHaveBeenJobWith(
            'phpspec_php_5_6',
            'registry.some.pl/ci/php:5.6-ci',
            'transfer-changes-phase',
            ['phing composer-dev', 'phing phpspec'],
            ['master'],
            []
        );
        $gitlabCIConfiguration->jobs()[1]->shouldHaveBeenJobWith(
            'lint_php_5_6',
            'registry.some.pl/ci/php:5.6-ci',
            'transfer-changes-phase',
            ['phing composer-dev', 'phing lint'],
            ['master'],
            []
        );
        $gitlabCIConfiguration->jobs()[2]->shouldHaveBeenJobWith(
            'join_coverage_raport',
            'registry.some.pl/ci/php:5.6-ci',
            'functional-tests-phase',
            ['phing composer-dev', 'phing coverage-raport'],
            ['master'],
            ['tags']
        );
        $gitlabCIConfiguration->jobs()[3]->shouldHaveBeenJobWith(
            'pdepend',
            'registry.some.pl/ci/php:5.6-ci',
            'dev-publish-phase',
            ['phing composer-dev', 'phing phpdepend'],
            ['master'],
            ['tags']
        );
        $gitlabCIConfiguration->jobs()[4]->shouldHaveBeenJobWith(
            'percentage_coverage',
            'registry.some.pl/ci/php:5.6-ci',
            'dev-publish-phase',
            ['phing composer-dev', 'phing coverage-check'],
            ['master'],
            ['tags']
        );
        $gitlabCIConfiguration->jobs()[5]->shouldHaveBeenJobWith(
            'clean_up',
            'registry.some.pl/ci/php:5.6-ci',
            'artifact-clean',
            ['phing composer-dev', 'phing artifact-clean'],
            [],
            ['tags']
        );

    }

    function it_should_throw_exception_if_file_doesnt_exists()
    {
        $this->shouldThrow(PrivateRunnerException::class)->during('createFromYaml', [vfsStream::url('root/test/.gitlab-ci.yml')]);
    }

    public function getMatchers()
    {
        return [
            'haveBeenJobWith' => function(Job $subject, $jobName, $imageName, $stage, array $scripts, array $exceptList, array $onlyList) {
                $isSatisfied  = true;

                $isSatisfied = $subject->jobName() == $jobName ? $isSatisfied : false;
                $isSatisfied = $subject->imageName() == $imageName ? $isSatisfied : false;
                $isSatisfied = $subject->stage()->name() == $stage ? $isSatisfied : false;
                $isSatisfied = count($subject->scripts()) == count($scripts) ? $isSatisfied : false;
                $isSatisfied = count($subject->exceptList()) == count($exceptList) ? $isSatisfied : false;
                $isSatisfied = count($subject->onlyList()) == count($onlyList) ? $isSatisfied : false;

                for ($i = 0; $i < count($subject->scripts()); $i++) {
                    $isSatisfied = $subject->scripts()[$i] == $scripts[$i] ? $isSatisfied : false;
                }

                for ($i = 0; $i < count($subject->exceptList()); $i++) {
                    $isSatisfied = $subject->exceptList()[$i] == $exceptList[$i] ? $isSatisfied : false;
                }

                for ($i = 0; $i < count($subject->onlyList()); $i++) {
                    $isSatisfied = $subject->onlyList()[$i] == $onlyList[$i] ? $isSatisfied : false;
                }

                return $isSatisfied;
            }
        ];
    }

    private function getGitlabConfiguration()
    {
        return 
'
stages:
  - "transfer-changes-phase"
  - "functional-tests-phase"
  - "dev-publish-phase"
  - "artifact-clean"
# Trzeba uruchomic wspoldzielony volumin dla raportow oraz artefaktow
cache:
#    Cache per project
  key: "$CI_PROJECT_ID"
  paths:
#    Global cache between all builds
    - "vendor/"
    - "bin/"

variables:
  COMPOSER_CACHE_DIR: "/cache/composer"

# PHP 5.6

phpspec_php_5_6:
  image: "registry.some.pl/ci/php:5.6-ci"
  stage: "transfer-changes-phase"
  script:
    - "phing composer-dev"
    - "phing phpspec"
  except:
    - "master"

lint_php_5_6:
  image: "registry.some.pl/ci/php:5.6-ci"
  stage: "transfer-changes-phase"
  script:
    - "phing composer-dev"
    - "phing lint"
  except:
    - "master"

join_coverage_raport:
  image: "registry.some.pl/ci/php:5.6-ci"
  stage: "functional-tests-phase"
  script:
    - "phing composer-dev"
    - "phing coverage-raport"
  only:
    - "tags"
  except:
    - "master"

pdepend:
  image: "registry.some.pl/ci/php:5.6-ci"
  stage: "dev-publish-phase"
  script:
    - "phing composer-dev"
    - "phing phpdepend"
  only:
    - "tags"
  except:
    - "master"

percentage_coverage:
  image: "registry.some.pl/ci/php:5.6-ci"
  stage: "dev-publish-phase"
  script:
    - "phing composer-dev"
    - "phing coverage-check"
  only:
    - "tags"
  except:
    - "master"

# Kiedy nie master artefakt powinnien zostac usuniety
clean_up:
  image: "registry.some.pl/ci/php:5.6-ci"
  stage: "artifact-clean"
  script:
    - "phing composer-dev"
    - "phing artifact-clean"
  only:
    - "tags"

# PHP 7

#phpspec_php_7_0:
#  image: "registry.some.pl/php:7.0-ci"
#  stage: "transfer-changes-phase"
#  script:
#    - "phing composer-dev"
#    - "phing -D compabilityMode=1 phpspec"
#  except:
#    - "master"
';
    }
    
}
