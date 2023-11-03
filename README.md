
# EventManagement Api

This projects helps in management of party palace events. It is used by event managers. This project provides the required API for the application.

## Requirements

- PHP > 8.0.2

## Project Info

- Laravel Framework: 9.2
- Api: REST API (Laravel Passport)
- Postman collection: https://www.getpostman.com/collections/c68661ff91aa360f8099

## Installation

Clone the project from git.
Create database. Create and setup .env file with database information. Then,

```bash
  composer install
  php artisan migrate
  php artisan passport:install
  php artisan storage:link
  php artisan key:generate
```

## Passport Setup

This project utilizes laravel passport for authentication. The default client id and secret
key is kept in env file. Get the values from oauth_clients table in database and put the
values in following keys in .env.

```code
    PASSPORT_CLIENT_ID=
    PASSPORT_CLIENT_SECRET=
```