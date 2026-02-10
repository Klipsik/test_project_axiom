# Приложение для поиска и фильтрации товаров

Веб-приложение для поиска и фильтрации товаров, построенное на Laravel 12 и Vue 3.

> **Примечание:** Это тестовое задание, демонстрирующее навыки backend-разработки, принципы SOLID и лучшие практики Laravel.

## Функционал

### Backend (Laravel 12)

- REST API endpoint для поиска товаров (`GET /api/products`)
- Расширенная фильтрация по: названию, диапазону цен, категории, наличию, рейтингу
- Сортировка: по цене (возр/убыв), рейтингу, дате создания
- Поддержка пагинации
- Многоуровневая стратегия кэширования:
  - Базовый кэш (долгосрочный) для простых запросов
  - Кэш отфильтрованных результатов (краткосрочный) для сложных запросов
  - Автоматическая инвалидация кэша при изменении данных
- Архитектура Service Layer с соблюдением принципов SOLID
- Паттерн Repository для доступа к данным
- Сидер для генерации тестовых данных (1000 товаров, 10 категорий)

### Frontend (Vue 3 + Tailwind CSS)

- Адаптивный интерфейс поиска товаров
- Фильтры с debounce для оптимизации запросов
- Выбор количества товаров на странице
- Пагинация с навигацией
- Отображение товаров в виде карточек

## Требования

- PHP >= 8.2
- Composer
- Bun или Node.js >= 18.x и npm
- SQLite (или MySQL/PostgreSQL)

## Установка

1. **Клонируйте репозиторий:**

   ```bash
   git clone https://github.com/Klipsik/test_project_axiom.git
   cd test_project_axiom
   ```

2. **Установите зависимости PHP:**

   ```bash
   composer install
   ```

3. **Настройте окружение:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Настройте базу данных в `.env`:**

   ```env
   DB_CONNECTION=sqlite
   # Или используйте MySQL/PostgreSQL
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=test_1
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

   Если используете SQLite, убедитесь что файл существует:

   ```bash
   touch database/database.sqlite
   ```

5. **Выполните миграции:**

   ```bash
   php artisan migrate
   ```

6. **Заполните базу данных тестовыми данными:**

   ```bash
   php artisan db:seed --class=ProductSeeder
   ```

7. **Установите зависимости Node.js:**

   ```bash
   npm install
   ```

8. **Соберите фронтенд:**

   ```bash
   npm run build
   ```

## Запуск

### Режим разработки

1. **Запустите Laravel сервер:**

   ```bash
   php artisan serve
   ```

2. **В отдельном терминале запустите Vite для hot-reload:**

   ```bash
   npm run dev
   ```

3. **Откройте в браузере:**

   ```
   http://localhost:8000
   ```

## Тестирование

### Веб-интерфейс

Откройте в браузере главную страницу:

```
http://localhost:8000
```

Доступные функции:
- Поиск по названию
- Фильтр по цене (от/до)
- Выбор категории
- Фильтр по наличию
- Минимальный рейтинг
- Сортировка результатов
- Выбор количества товаров на странице

### API Endpoints

#### Получить список товаров с фильтрами:

```
GET /api/products
```

**Query параметры:**

- `q` - поиск по названию (подстрока)
- `price_from` - минимальная цена
- `price_to` - максимальная цена
- `category_id` - ID категории
- `in_stock` - наличие (true/false)
- `rating_from` - минимальный рейтинг
- `sort` - сортировка: `price_asc`, `price_desc`, `rating_desc`, `newest`
- `per_page` - количество на странице (по умолчанию 15, максимум 100)
- `page` - номер страницы

**Примеры запросов:**

```bash
# Поиск по названию
curl "http://localhost:8000/api/products?q=товар"

# Фильтр по цене и сортировка
curl "http://localhost:8000/api/products?price_from=100&price_to=1000&sort=price_asc"

# Фильтр по категории и наличию
curl "http://localhost:8000/api/products?category_id=1&in_stock=true"

# Полный пример с пагинацией
curl "http://localhost:8000/api/products?q=название&price_from=50&price_to=500&category_id=2&in_stock=true&rating_from=4.0&sort=rating_desc&per_page=30&page=1"
```

#### Получить список категорий:

```
GET /api/categories
```

**Пример:**

```bash
curl "http://localhost:8000/api/categories"
```

## Структура проекта

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── ProductController.php    # Контроллер товаров
│   │       └── CategoryController.php   # Контроллер категорий
│   └── Requests/
│       └── ProductIndexRequest.php       # Валидация запросов
├── Models/
│   ├── Product.php                       # Модель товара
│   └── Category.php                      # Модель категории
├── Services/
│   ├── ProductService.php                # Бизнес-логика
│   └── ProductCacheService.php            # Стратегия кэширования
├── Repositories/
│   └── ProductRepository.php              # Слой доступа к данным
└── Observers/
    └── ProductObserver.php                 # События модели (инвалидация кэша)

database/
├── migrations/
│   ├── create_categories_table.php
│   ├── create_products_table.php
│   └── add_composite_indexes_to_products_table.php
├── seeders/
│   └── ProductSeeder.php                  # Сидер для 1000 товаров
└── factories/
    ├── CategoryFactory.php
    └── ProductFactory.php

resources/
├── js/
│   ├── app.js                             # Точка входа Vue
│   └── components/
│       └── ProductSearch.vue               # Компонент поиска
└── views/
    └── products.blade.php                 # Blade шаблон

routes/
├── api.php                                 # API маршруты
└── web.php                                 # Web маршруты

config/
└── products.php                            # Конфигурация товаров (TTL кэша, пагинация)
```

## Архитектура

Приложение следует принципам SOLID и лучшим практикам Laravel:

- **Service Layer**: Бизнес-логика отделена от контроллеров
- **Repository Pattern**: Абстракция доступа к данным
- **Observer Pattern**: Автоматическая инвалидация кэша при событиях модели
- **Многоуровневое кэширование**: Умная стратегия кэширования в зависимости от сложности запроса

### Стратегия кэширования

- **Базовый кэш** (60 минут): Кэширует все товары с категориями для простых запросов
- **Кэш отфильтрованных результатов** (15-30 минут): Кэширует отфильтрованные результаты для сложных запросов
- **Автоматическая инвалидация**: Кэш инвалидируется при создании/обновлении/удалении товара

Настройки кэша можно изменить в `config/products.php` или через переменные окружения:
- `PRODUCT_CACHE_BASE_TTL` (по умолчанию: 60 минут)
- `PRODUCT_CACHE_FILTERED_TTL` (по умолчанию: 15 минут)
- `PRODUCT_CACHE_POPULAR_TTL` (по умолчанию: 30 минут)

## Технологии

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue 3, Tailwind CSS 4, Vite
- **База данных:** SQLite (по умолчанию), поддержка MySQL/PostgreSQL
- **Кэш:** Поддержка драйверов Redis, Memcached, Database, File

## Запуск тестов

```bash
php artisan test
```

Покрытие тестами включает:
- Unit тесты для моделей, репозиториев, сервисов, DTO и наблюдателей
- Feature тесты для API endpoints
- Тесты фильтрации и сортировки
- Тесты пагинации
- Тесты инвалидации кэша

**Результаты тестов:** 55 тестов пройдено (287 утверждений)

## Качество кода

Проект следует лучшим практикам:
- **Принципы SOLID** - Инверсия зависимостей, Единственная ответственность и др.
- **Принцип DRY** - Отсутствие дублирования кода
- **Laravel Way** - Использование хелперов и конвенций Laravel
- **Паттерн Repository** - Абстракция доступа к данным
- **Service Layer** - Разделение бизнес-логики
- **Внедрение зависимостей** - Использование контрактов/интерфейсов вместо конкретных классов
- **Комплексное тестирование** - Высокое покрытие тестами (unit и feature)

## CI/CD

Настроен GitHub Actions workflow для:
- Автоматического тестирования при push/PR
- Отчетов о покрытии кода (минимум 60%)
- Проверки совместимости с PHP 8.3