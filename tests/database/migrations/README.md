# Migrations
Folder contains migrations that are running on each test `setUp`.

As we use in memory database when running test and using `doctrine:migrations:diff` command to generate the migration we must keep here only one single migration file that will create schema needed for tests to run.

Look into on [seeders](../seeders) folder for more documentation on how to seed database with data before tests.
