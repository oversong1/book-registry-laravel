#!/bin/sh
# Roda toda vez que o container "php" sobe, ANTES do Apache.
# Na primeira vez prepara tudo; nas proximas, so confere e segue.
set -e   # se qualquer comando falhar, para o script (e o erro aparece no log)

cd /var/www/html

# 1. Dependencias do PHP: a pasta vendor nao vai pro Git
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] composer install..."
    composer install --no-interaction --prefer-dist
fi

# 2. .env e chave da aplicacao: so na primeira vez
PRIMEIRA_VEZ=0
if [ ! -f .env ]; then
    echo "[entrypoint] criando .env..."
    cp .env.example .env
    php artisan key:generate --force
    PRIMEIRA_VEZ=1
fi

# 3. CSS/JS compilado (Tailwind): a pasta public/build nao vai pro Git
if [ ! -f public/build/manifest.json ]; then
    echo "[entrypoint] npm install + build..."
    npm install
    npm run build
fi

# 4. Permissao de escrita pro Apache (www-data)
chmod -R 777 storage bootstrap/cache

# 5. Tabelas. O MySQL ja esta pronto (healthcheck + depends_on no docker-compose.yml)
php artisan migrate --force

# 6. Usuarios e livros de demonstracao: so na primeira vez
if [ "$PRIMEIRA_VEZ" = "1" ]; then
    echo "[entrypoint] seed..."
    php artisan db:seed --force
fi

echo "[entrypoint] pronto: http://localhost:8098"

# exec "$@": troca este script pelo comando principal do container (o Apache)
exec "$@"