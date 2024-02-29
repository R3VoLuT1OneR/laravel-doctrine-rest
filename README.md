# laravel-doctrine-rest

[![Build Status](https://travis-ci.org/R3VoLuT1OneR/laravel-doctrine-rest.svg?branch=master)](https://travis-ci.org/R3VoLuT1OneR/laravel-doctrine-rest)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/R3VoLuT1OneR/laravel-doctrine-rest/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/R3VoLuT1OneR/laravel-doctrine-rest?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/R3VoLuT1OneR/laravel-doctrine-rest/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/R3VoLuT1OneR/laravel-doctrine-rest?branch=master)

Laravel 5 Rest API for Doctrine ORM

## Versions

| Version | Supported Laravel Versions |
|:--------|:---------------------------|
| ^0.2.0  | 7.x                        |
| ^0.3.0  | 8.x                        |
| ^0.4.0  | 9.x                        |
| ^0.5.0  | 10.x                        |

## Upgrade Guide

### From 0.4.x to 0.5.x

  * Upgrade composer dependencies `composer require pavelz/laravel-doctrine-rest:^0.5.0`
  * Remove `LaravelDoctrine\ORM\Types\Json::class` from `config/doctrine.php` at `custom_types` if you have it there.
  * Set `'namespace' => 'DoctrineProxies'` in `config/doctrine.php` at `managers.*.proxies` section.

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
