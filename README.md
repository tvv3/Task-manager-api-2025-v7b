## Version v7b of the Laravel 11 Tasks API
This is a Laravel 11 api for task management. It works with the v7b version of the separate front end made with vue js 3. 

API-ul foloseste autentificare Sanctum și cookie-uri.

## Pre-rechizite

- PHP >= 8.1
- Composer
- MySQL 
- Node.js (optional)

## Instalare

1. Clonează repository:
   ```bash
   git clone <URL_REPO_API>
   cd nume_proiect_api

2. Instalează dependențele PHP:

composer install

3. Configurează fișierul .env:

cp .env.example .env


4. Setează in .env:

DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

SANCTUM_STATEFUL_DOMAINS=localhost:3000

SESSION_DOMAIN=localhost

5. Generează cheia aplicației:

php artisan key:generate


6. Rulează migrațiile:

php artisan migrate

7. Rulare locală
php artisan serve --port=8000

API-ul va fi accesibil la http://localhost:8000.

8. Decomenteaza in routes/api.php ruta RegisterAdmin si creeaza cu Postman cel putin un user de administrator si apoi comenteaza din nou ruta speciala pentru RegisterAdmin. 

9. Cloneaza si front end-ul separat in vue js 3 si ruleaza-l pe local 
folosind portul 3000. Creeaza alti useri cu rolul user si apoi cu acestia creeaza taskuri, echipe si comentarii. 
Notes pentru Frontend

Asigură-te că frontend-ul Vue 3 rulează pe localhost:3000.

Sanctum folosește cookie-uri, deci CORS trebuie configurat corect.

Exemplu de setări CORS în config/cors.php (daca fisierul nu exista trebuie folosita comanda:
 php artisan config:publish cors
pentru a-l crea):

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // 🔑 required for cookies
];

In bootstrap/api trebuie pus:

->withMiddleware(function (Middleware $middleware) {
    $middleware->statefulApi();
    })

In config/session.php trebuie pus:

'same_site' => 'lax', //in loc de env('SESSION_SAME_SITE', 'lax'),

Verifica fisierele config/session.php si config/sanctum.php.

Apoi se va rula intai backend-ul pe portul 8000 si apoi frontend-ul pe portul 3000. 

## Demo video 

Link: https://youtu.be/BOvmXLB_cBg