# Gitlab Private Runner
[![Build Status](https://travis-ci.org/madkom/gitlab-ci-private-runner.svg?branch=master)](https://travis-ci.org/madkom/gitlab-ci-private-runner)
[![Coverage Status](https://coveralls.io/repos/github/madkom/gitlab-ci-private-runner/badge.svg?branch=master)](https://coveralls.io/github/madkom/gitlab-ci-private-runner?branch=master)
[![Latest Stable Version](https://poser.pugx.org/madkom/gitlab-ci-private-runner/v/stable)](https://packagist.org/packages/madkom/gitlab-ci-private-runner)
[![Total Downloads](https://poser.pugx.org/madkom/gitlab-ci-private-runner/downloads)](https://packagist.org/packages/madkom/gitlab-ci-private-runner)
[![License](https://poser.pugx.org/madkom/gitlab-ci-private-runner/license)](https://packagist.org/packages/madkom/gitlab-ci-private-runner)
[![PHP Versions](http://php-eye.com/badge/madkom/gitlab-ci-private-runner/tested.svg)](http://php-eye.com/package/madkom/gitlab-ci-private-runner)

`Runs` Gitlab CI jobs on developer machine, `without need to push the code` to the repository.
This project doesn't aim to replace Gitlab CI instead it helps developers to test even the most complicated jobs, without need
to commit the code. 

## Console commands
See usage of the command by running bin/private-gitlab-runner

### List all possible jobs
`bin/private-gitlab-runner private-gitlab-ci:job:list`  

     Responsible for listing all possible jobs

### List all stages
`bin/private-gitlab-runner private-gitlab-ci:stage:list`  

     Responsible for listing all possible stages

### Run job
`bin/private-gitlab-runner private-gitlab-ci:job:run job_name` 

     Responsible for running specific job in docker environment. 