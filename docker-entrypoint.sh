#!/bin/bash

pwd
ls -la /var/www/html/var/cache

echo >&2 "========================== ============================================="

ls -la /var/www/html/var/cache/prod

composer run-script build
composer run-script clearme

chmod -R 777 var
chown -R www-data:www-data var
echo >&2 "=============================================="

ls -la /var/www/html/var/cache/prod

echo >&2 "================START=============================="

#composer run-script build

echo >&2 "================DONE=============================="
ls -la /var/www/html/var/cache/
ls -la /var/www/html/var/cache/prod


#composer run-script clearme

set -e

if [ ! -f /usr/local/etc/php/php.ini ]; then
	cat <<EOF > /usr/local/etc/php/php.ini
date.timezone = "${PHP_INI_DATE_TIMEZONE}"
always_populate_raw_post_data = -1
memory_limit = ${PHP_MEMORY_LIMIT}
file_uploads = On
upload_max_filesize = ${PHP_MAX_UPLOAD}
post_max_size = ${PHP_MAX_UPLOAD}
max_execution_time = ${PHP_MAX_EXECUTION_TIME}
EOF
fi

# Ensure the MySQL Database is created
php /makedb.php "$MAUTIC_DB_HOST" "$MAUTIC_DB_USER" "$MAUTIC_DB_PASSWORD" "$MAUTIC_DB_NAME"

# Write the database connection to the config so the installer prefills it
if ! [ -e /var/www/html/app/saved/local.php ]; then
        php /makeconfig.php

        chown www-data:www-data /var/www/html/app/saved/local.php
        chmod -R 777 /var/www/html/app/saved/
        chmod -R 777 /var/www/html/app/saved/local.php

        mkdir -p /var/www/html/app/logs
        chown -R www-data:www-data /var/www/html/app/logs
        chown -R www-data:www-data /var/www/html/app/saved
fi

if [[ "$MAUTIC_RUN_CRON_JOBS" == "true" ]]; then
    if [ ! -e /var/log/cron.pipe ]; then
        mkfifo /var/log/cron.pipe
        chown www-data:www-data /var/log/cron.pipe
    fi
    (tail -f /var/log/cron.pipe | while read line; do echo "[CRON] $line"; done) &
    CRONLOGPID=$!
    cron -f &
    CRONPID=$!
else
    echo >&2 "Not running cron as requested."
fi

echo >&2 "=========================== LAST THING ============================================="
chown -R www-data:www-data var
chmod -R 777 var
ls -la /var/www/html/var/cache/
ls -la /var/www/html/var/cache/prod

echo >&2 "============FME===================="

#php bin/console mautic:plugins:reload

echo >&2 "============/FME===================="

echo >&2 "========================================================================"

"$@" &
MAINPID=$!

shut_down() {
    if [[ "$MAUTIC_RUN_CRON_JOBS" == "true" ]]; then
        kill -TERM $CRONPID || echo 'Cron not killed. Already gone.'
        kill -TERM $CRONLOGPID || echo 'Cron log not killed. Already gone.'
    fi
    kill -TERM $MAINPID || echo 'Main process not killed. Already gone.'
}
trap 'shut_down;' TERM INT

# wait until all processes end (wait returns 0 retcode)
while :; do
    if wait; then
        break
    fi
done