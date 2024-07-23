# Task Manager API

This is a Laravel-based API for managing tasks.

## Setup

1. Clone the repository
2. Run composer install
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan migrate`
5. Run `php artisan serve`

## API Documentation

The full API documentation is available as a Postman collection:

[Task Manager API Postman Collection](https://documenter.getpostman.com/view/28699003/2sA3kVmgrU)

To use the API:

1. Register a new user or login to get an access token
2. Use the access token in the Authorization header for all protected routes:
   `Authorization: Bearer your_access_token_here`

## Running Tests

Run `php artisan test` to execute the feature tests.