# laravel-doctrine-rest

## Development
Use `docker-compose` for running PHPUnit tests even if your local PHP runtime version doesn't match librariy one.

To install dependencies and run the tests
```shell
docker compose run php
```

To get shell into Docker environment run
```shell
docker compose run php sh
```

### Testing
You can find all the testing documentation in the [./tests](./tests) folder.

## Roadmap
  - [ ] Create default global error handler or write down documentation how to create such one.
        How to handle missing route\endpoint 404 and internal 500 errors.
        