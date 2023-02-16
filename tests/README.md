# PHPUnit

## Running
Run the tests in the docker environment by running next command:
```shell
docker compose run php phpunit
```

## Laravel
We install Laravel as composer dev dependency and putting all it's files into the [tests/laravel]() folder. Doctrine is set up in this laravel instance and some testing entities created.

## Database
We are using SQLite in memory database. Each time we start the tests we apply migrations from the [./database/migrations](./database/migrations) folder. After change in the entities metadata we need to create a new schema in our migration folder.

Because we use in memory database for migrations command that generates new migrations looking into entities metadata.
```shell
php tests/artisan doctrine:migrations:diff
```
Command will generate full schema migration file please create an old one.

Congrats you can run your tests with a new schema.

### Seeders
You can find seeders in the [./database/seeders](./database/seeders) folder. You can run them in tests by using `seed` method. The initialization seeder is running on each test `setUp` after migrations refresh.

```php
$this->seed(\Database\Seeders\SetUpSeeder::class);
```
