```shell
./vendor/bin/sail php artisan migrate:fresh
./vendor/bin/sail php artisan db:seed
./vendor/bin/sail php artisan test --testsuite=Feature --stop-on-failure
```
