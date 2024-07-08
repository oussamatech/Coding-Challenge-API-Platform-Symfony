# Coding-Challenge-API-Platform-Symfony


# Library API

This is a RESTful API for managing a simple library system. The API allows users to perform CRUD operations on books, authors, and categories. The application is built using the Symfony framework and API Platform, and it is containerized using Docker.

## Features

- CRUD operations for books, authors, and categories.
- Filter books by publication date.
- Filter authors by name.
- Containerized using Docker.
- Integration tests for API endpoints.
- Basic CI/CD pipeline for automated testing and deployment.

## Requirements

- Docker
- Docker Compose

## Setup

1. Clone the repository:

```bash
git clone https://github.com/oussamatech/Coding-Challenge-API-Platform-Symfony.git
```

## Running Tests

To run the integration tests, use the following commands:

```bash
docker-compose exec php bin/phpunit
```

or with a custom bootstrap file:

```bash
docker-compose exec php php bin/phpunit --bootstrap=config/bootstrap.php
```
