# Fomo Store Assessment

A simple Laravel 13 REST API for flash sale product ordering.

## Features

- Product Management
- Flash Sale Discount
- Order Creation
- Stock Validation
- Race Condition Prevention using Database Transaction + Row Lock
- PostgreSQL
- Docker Compose

## Tech Stack

- Laravel 13
- PHP 8.4
- PostgreSQL 16
- Docker Compose

## Installation

```bash
git clone <repository>

cd backend

cp .env.example .env

docker compose up -d --build

docker compose exec fomo_app php artisan key:generate

docker compose exec fomo_app php artisan migrate --seed
```

## API

### List Products

```
GET /api/products
```

### Create Order

```
POST /api/orders
```

Example request

```json
{
    "email": "customer@test.com",
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        }
    ]
}
```

## Running Tests

```bash
docker compose exec fomo_app php artisan test
```

## Notes

To prevent overselling during flash sales, product rows are locked using:

- Database Transactions
- `lockForUpdate()`