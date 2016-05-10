# Gitlab Private Runner

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