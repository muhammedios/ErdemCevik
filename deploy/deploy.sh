#!/usr/bin/env bash
set -euo pipefail
umask 022
export PATH="/usr/local/lsws/lsphp82/bin:$PATH"
cd /home/dterdemcevik.com/app

exec 9>/home/dterdemcevik.com/.deploy.lock
flock -n 9 || { echo 'Başka bir deploy çalışıyor.'; exit 1; }

target=${1:?Yayınlanacak commit gerekli}
git merge-base --is-ancestor HEAD "$target"
test -z "$(git status --porcelain --untracked-files=no)" || {
    echo 'Sunucuda değiştirilmiş kod var; deploy durduruldu.'
    exit 1
}

backup="/home/dterdemcevik.com/backups/$(date -u +%Y%m%d-%H%M%S)-$$"
mkdir -p "$backup"
chmod 700 /home/dterdemcevik.com/backups "$backup"
git rev-parse HEAD > "$backup/commit.txt"
cp .env "$backup/.env"
php -r '
$source = new SQLite3("database/database.sqlite");
$target = new SQLite3($argv[1]);
if (!$source->backup($target)) { exit(1); }
' "$backup/database.sqlite"

git merge --ff-only "$target"
php /usr/bin/composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan filament:assets
php artisan optimize
curl --fail --silent --show-error --max-time 30 --output /dev/null https://dterdemcevik.com/tr
echo 'Deploy tamamlandı:'
git log -1 --oneline
