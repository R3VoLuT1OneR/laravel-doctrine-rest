# Seeders
The folder for keeping seeders that can be used for seeders.

The [SetUpSeeder](./SetUpSeeder.php) seeded on each test `setUp`.

## New Seeder
Create a new class in the folder with any name for exampe `NewSeeder` and create a public function `run(EntityManager $em)` in order to run the seeder you can use `$this->seed(NewSeeder::class);` in the test `setUp` or in test function itself.

```php
class NewSeeder {
    public function run(EntityManager $em): void
    {
        // ...
    }
}
```
