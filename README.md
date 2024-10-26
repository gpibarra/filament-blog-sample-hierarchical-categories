Steps:

```
git clone [url]
cd [repo]

cp .env.example .env
composer install
php artisan key:generate

touch database/database.sqlite
php artisan migrate
php artisan db:seed

php artisan filament:upgrade

php artisan serve
```

Goto `http://localhost:8000` and login with the following credentials:
```
user: admin@mail.com
pass: pass123
```

