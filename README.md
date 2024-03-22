```shell
composer install

./vendor/bin/sail up -d

php artisan key:generate

./vendor/bin/sail php artisan migrate:fresh
./vendor/bin/sail php artisan queue:table
./vendor/bin/sail php artisan db:seed
./vendor/bin/sail php artisan test --testsuite=Feature --stop-on-failure
```



```shell
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}
```


## Resources
- GitLab: [Rules](https://docs.gitlab.com/ee/ci/yaml/index.html#rules)
- GitLab: [Common `if` clauses for `rules`](https://docs.gitlab.com/ee/ci/jobs/job_control.html#common-if-clauses-for-rules)
- GitLab: [Cache PHP dependencies](https://docs.gitlab.com/ee/ci/caching/#cache-php-dependencies)
- OWASP: [Laravel Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Laravel_Cheat_Sheet.html)
