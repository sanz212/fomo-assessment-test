# FomoStore API

REST API untuk sistem product ordering dengan fitur flash sale stock protection.

Project ini dibuat menggunakan Laravel 13 dengan fokus pada clean architecture, transaction handling, API design, dan pencegahan race condition pada inventory.

---

## Tech Stack

- Laravel 13
- PHP 8.4
- PostgreSQL
- Docker Compose
- REST API
- Laravel API Resource
- Service Layer
- Database Transaction
- Row Locking (`lockForUpdate`)

---

# Installation

## Requirements

Pastikan sudah terinstall:

- Docker
- Docker Compose

---

## Clone Repository

```bash
git clone https://github.com/sanz212/fomo-assessment-test.git
```
```
cd fomo-assessment-test
```

---

## Environment Setup

Copy environment file:

```bash
cp .env.example .env
```

---

## Run Docker Container

Build dan jalankan container:

```bash
docker compose up -d --build
```

---

## Install Dependencies

Install composer dependencies:

```bash
docker compose exec fomo_app composer install
```

---

## Generate Application Key

```bash
docker compose exec fomo_app php artisan key:generate
```

---

## Database Setup

Jalankan migration dan seeder:

```bash
docker compose exec fomo_app php artisan migrate:fresh --seed
```

---

# Running Application

API dapat diakses melalui:

```
http://localhost:8000
```

---

# API Documentation

## Products

---

## Get Product List

### Endpoint

```
GET /api/products
```

### Response

```json
{
    "status": "success",
    "message": "Products retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Kacamata Super",
            "price": "1200000.00",
            "discount_percentage": "50.00",
            "discount_price": "600000.00",
            "stock": 100
        }
    ]
}
```

---

## Get Product Detail

### Endpoint

```
GET /api/products/{productId}
```

Example:

```
GET /api/products/1
```

---

# Orders

---

## Create Order

Membuat order baru dan mengurangi stock produk.

### Endpoint

```
POST /api/orders
```

### Request Body

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

### Response

```json
{
    "status": "success",
    "message": "Order created successfully",
    "data": {
        "id": 1,
        "email": "customer@test.com",
        "order_items": [
            {
                "product_id": 1,
                "product_name": "Kacamata Super",
                "quantity": 2,
                "unit_price": "600000.00",
                "subtotal": "1200000.00"
            }
        ],
        "status": "completed",
        "total": "1200000.00",
        "created_at": "2026-07-20T14:15:15.000000Z"
    }
}
```

---

## Get Order Detail

Mengambil detail order menggunakan order ID dan email customer.

### Endpoint

```
GET /api/orders/{orderId}?email=customer@test.com
```

Example:

```
GET /api/orders/1?email=customer@test.com
```

---

# Testing

Jalankan automated test:

```bash
docker compose exec fomo_app php artisan test
```

Expected result:

```
PASS Tests\Unit\ExampleTest

PASS Tests\Feature\ExampleTest

PASS Tests\Feature\FlashSaleRaceConditionTest

Tests: 3 passed
```

---

# Architecture

Project menggunakan pemisahan tanggung jawab:

```
Controller
    |
    |
Form Request Validation
    |
    |
Service Layer
    |
    |
Models
    |
    |
Database
```

---

# Order Processing Flow

Ketika customer membuat order:

1. Request divalidasi menggunakan Form Request.
2. Product di-lock menggunakan database row locking.
3. Stock diperiksa.
4. Order dibuat.
5. Order item dibuat sebagai snapshot transaksi.
6. Stock produk dikurangi.

---

# Flash Sale Race Condition Protection

Untuk mencegah overselling saat banyak customer melakukan pembelian secara bersamaan digunakan:

```php
Product::lockForUpdate()
```

Database transaction memastikan hanya satu proses yang dapat melakukan perubahan stock pada waktu yang sama.

Contoh:

```
User A membeli stock terakhir
        |
        lock product row
        |
        update stock
        |
        commit transaction


User B menunggu sampai transaction selesai
```

Dengan pendekatan ini stock tidak akan menjadi minus atau terjual melebihi jumlah tersedia.

---

# Database Structure

## Products Table

Menyimpan informasi produk dan inventory.

Columns:

```
id
name
price
discount_percentage
discount_price
stock
created_at
updated_at
```

---

## Orders Table

Menyimpan data transaksi customer.

Columns:

```
id
email
total
status
created_at
updated_at
```

---

## Order Items Table

Menyimpan detail produk saat transaksi.

Data disimpan sebagai snapshot agar histori transaksi tetap benar walaupun produk berubah.

Columns:

```
id
order_id
product_id
quantity
price
created_at
updated_at
```

---

# Error Handling

API menggunakan format response yang konsisten.

Example:

```json
{
    "status": "error",
    "message": "Resource not found."
}
```

Handled errors:

- 404 Resource Not Found
- 403 Forbidden

---

# Features

Implemented:

- Product listing
- Product detail
- Create order
- Order detail
- Discount calculation
- Inventory management
- Transaction handling
- Flash sale race condition protection
- API Resource transformation
- Request validation
- Automated testing

---

# License

This project is created for assessment purposes.