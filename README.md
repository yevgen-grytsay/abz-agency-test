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
