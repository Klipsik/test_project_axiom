# Product Search & Filter Application

A web application for product search and filtering built with Laravel 12 and Vue 3.

> **Note:** This is a test assignment project demonstrating backend development skills, SOLID principles, and Laravel best practices.

## Features

### Backend (Laravel 12)

- REST API endpoint for product search (`GET /api/products`)
- Advanced filtering by: name, price range, category, stock status, rating
- Sorting: by price (asc/desc), rating, creation date
- Pagination support
- Multi-level caching strategy:
  - Base cache (long-term) for simple queries
  - Filtered cache (short-term) for complex queries
  - Automatic cache invalidation on data changes
- Service Layer architecture following SOLID principles
- Repository pattern for data access
- Seeder for generating test data (1000 products, 10 categories)

### Frontend (Vue 3 + Tailwind CSS)

- Responsive product search interface
- Filters with debounce for request optimization
- Per-page items selection
- Pagination with navigation
- Product cards display

## Requirements

- PHP >= 8.2
- Composer
- Bun or Node.js >= 18.x and npm
- SQLite (or MySQL/PostgreSQL)

## Installation

1. **Clone the repository or extract the project:**

   ```bash
   cd /var/www/test_project
   ```

2. **Install PHP dependencies:**

   ```bash
   composer install
   ```

3. **Set up environment:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in `.env`:**

   ```env
   DB_CONNECTION=sqlite
   # Or use MySQL/PostgreSQL
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=test_1
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

   If using SQLite, ensure the file exists:

   ```bash
   touch database/database.sqlite
   ```

5. **Run migrations:**

   ```bash
   php artisan migrate
   ```

6. **Seed the database with test data:**

   ```bash
   php artisan db:seed --class=ProductSeeder
   ```

7. **Install Node.js dependencies:**

   ```bash
   npm install
   ```

8. **Build frontend:**

   ```bash
   npm run build
   ```

## Running

### Development Mode

1. **Start Laravel server:**

   ```bash
   php artisan serve
   ```

2. **In a separate terminal, start Vite for hot-reload:**

   ```bash
   npm run dev
   ```

3. **Open in browser:**

   ```
   http://localhost:8000
   ```

## Testing

### Web Interface

Open the main page in your browser:

```
http://localhost:8000
```

Available features:
- Name search
- Price filter (from/to)
- Category selection
- Stock status filter
- Minimum rating
- Result sorting
- Items per page selection

### API Endpoints

#### Get products list with filters:

```
GET /api/products
```

**Query parameters:**

- `q` - search by name (substring)
- `price_from` - minimum price
- `price_to` - maximum price
- `category_id` - category ID
- `in_stock` - stock status (true/false)
- `rating_from` - minimum rating
- `sort` - sorting: `price_asc`, `price_desc`, `rating_desc`, `newest`
- `per_page` - items per page (default 15, max 100)
- `page` - page number

**Request examples:**

```bash
# Search by name
curl "http://localhost:8000/api/products?q=product"

# Price filter and sorting
curl "http://localhost:8000/api/products?price_from=100&price_to=1000&sort=price_asc"

# Category and stock filter
curl "http://localhost:8000/api/products?category_id=1&in_stock=true"

# Full example with pagination
curl "http://localhost:8000/api/products?q=name&price_from=50&price_to=500&category_id=2&in_stock=true&rating_from=4.0&sort=rating_desc&per_page=30&page=1"
```

#### Get categories list:

```
GET /api/categories
```

**Example:**

```bash
curl "http://localhost:8000/api/categories"
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── ProductController.php    # Product controller
│   │       └── CategoryController.php   # Category controller
│   └── Requests/
│       └── ProductIndexRequest.php       # Request validation
├── Models/
│   ├── Product.php                       # Product model
│   └── Category.php                      # Category model
├── Services/
│   ├── ProductService.php                # Business logic
│   └── ProductCacheService.php            # Caching strategy
├── Repositories/
│   └── ProductRepository.php              # Data access layer
└── Observers/
    └── ProductObserver.php                 # Model events (cache invalidation)

database/
├── migrations/
│   ├── create_categories_table.php
│   ├── create_products_table.php
│   └── add_composite_indexes_to_products_table.php
├── seeders/
│   └── ProductSeeder.php                  # Seeder for 1000 products
└── factories/
    ├── CategoryFactory.php
    └── ProductFactory.php

resources/
├── js/
│   ├── app.js                             # Vue entry point
│   └── components/
│       └── ProductSearch.vue               # Search component
└── views/
    └── products.blade.php                 # Blade template

routes/
├── api.php                                 # API routes
└── web.php                                 # Web routes

config/
└── products.php                            # Product configuration (cache TTL, pagination)
```

## Architecture

The application follows SOLID principles and Laravel best practices:

- **Service Layer**: Business logic separated from controllers
- **Repository Pattern**: Data access abstraction
- **Observer Pattern**: Automatic cache invalidation on model events
- **Multi-level Caching**: Smart caching strategy based on query complexity

### Caching Strategy

- **Base Cache** (60 minutes): Caches all products with categories for simple queries
- **Filtered Cache** (15-30 minutes): Caches filtered results for complex queries
- **Automatic Invalidation**: Cache is invalidated on product create/update/delete

Cache configuration can be adjusted in `config/products.php` or via environment variables:
- `PRODUCT_CACHE_BASE_TTL` (default: 60 minutes)
- `PRODUCT_CACHE_FILTERED_TTL` (default: 15 minutes)
- `PRODUCT_CACHE_POPULAR_TTL` (default: 30 minutes)

## Technologies

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue 3, Tailwind CSS 4, Vite
- **Database:** SQLite (default), MySQL/PostgreSQL support
- **Cache:** Supports Redis, Memcached, Database, File drivers

## Running Tests

```bash
php artisan test
```

Test coverage includes:
- Unit tests for models
- Feature tests for API endpoints
- Filter and sorting tests
- Pagination tests
