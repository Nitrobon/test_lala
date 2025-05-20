## Запуск проекта

1. Соберите и запустите контейнеры:

```bash
docker compose up -d --build
```

2. Войдите в контейнер PHP для установки зависимостей:

```bash
docker compose exec app bash
```

3. Внутри контейнера выполните:

```bash
composer install
```

4. Создайте базу данных и выполните миграции:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. (Опционально) Загрузите тестовые данные:

```bash
php bin/console doctrine:fixtures:load
```

## Доступ к проекту

После запуска, приложение будет доступно по адресу:
- http://localhost:8080

## Остановка проекта

Для остановки контейнеров выполните:

```bash
docker compose down
```

Для остановки контейнеров и удаления томов (данные базы данных будут потеряны):

```bash
docker compose down -v
```


Коллекция API для Postman лежит в файле Task API.postman_collection.json