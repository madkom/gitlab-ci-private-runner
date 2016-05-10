# Gitlab Private Runner
[![Build Status](https://travis-ci.org/madkom/gitlab-ci-private-runner.svg?branch=master)](https://travis-ci.org/madkom/gitlab-ci-private-runner)
[![Coverage Status](https://coveralls.io/repos/github/madkom/gitlab-ci-private-runner/badge.svg?branch=master)](https://coveralls.io/github/madkom/gitlab-ci-private-runner?branch=master)
[![Latest Stable Version](https://poser.pugx.org/madkom/gitlab-ci-private-runner/v/stable)](https://packagist.org/packages/madkom/gitlab-ci-private-runner)
[![Total Downloads](https://poser.pugx.org/madkom/gitlab-ci-private-runner/downloads)](https://packagist.org/packages/madkom/gitlab-ci-private-runner)
[![License](https://poser.pugx.org/madkom/gitlab-ci-private-runner/license)](https://packagist.org/packages/madkom/gitlab-ci-private-runner)

`Runs` Gitlab CI jobs on developer machine, `without need to push the code` to the repository.
This project doesn't aim to replace Gitlab CI instead it helps developers to test even the most complicated jobs, without need
to commit the code. 

## Console commands
Run example commands from the root of the project.

### List all possible jobs
`bin/private-gitlab-runner private-gitlab-ci:job:list [path_to_gitlab_ci.yml]`  

Example:
* Run command `bin/private-gitlab-runner private-gitlab-ci:job:list $PWD/.gitlab-ci.yml`

### List all stages
`bin/private-gitlab-runner private-gitlab-ci:stage:list [path_to_gitlab_ci.yml]`  

Example:
* `bin/private-gitlab-runner private-gitlab-ci:stage:list $PWD/.gitlab-ci.yml`

### Run job
`bin/private-gitlab-runner private-gitlab-ci:job:run [path_to_gitlab_ci.yml] [job_name] [optional-current-branch]`

Example:
* `bin/private-gitlab-runner private-gitlab-ci:job:run $PWD/.gitlab-ci.yml phpspec_php_5_6`