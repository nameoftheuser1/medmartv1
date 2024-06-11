# MedMart

MedMart is a web application built using Laravel, designed to manage products, suppliers, sales, and inventory. This project utilizes Vite for asset bundling and development.

## Table of Contents

- [MedMart](#medmart)
  - [Table of Contents](#table-of-contents)
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Database Migrations](#database-migrations)

## Requirements

Before you begin, ensure you have met the following requirements:

- PHP >= 8.0
- Composer
- Node.js and npm
- MySQL or any other database supported by Laravel

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/nameoftheuser1/medmartv1.git
    cd medmartv1
    ```

2. Install the PHP dependencies:

    ```bash
    composer install
    ```

3. Install the Node.js dependencies:

    ```bash
    npm install
    ```

## Configuration

1. Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```

2. Generate an application key:

    ```bash
    php artisan key:generate
    ```

3. Configure your database in the `.env` file:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

## Database Migrations

Run the database migrations to create the necessary tables:

```bash
php artisan migrate
