#!/bin/sh

set -e

if [ "$1" = 'php' ] && [ "$(id -u)" = '0' ]; then
    chown -R www-data /sspanel
fi

if [ ! -e '/sspanel/public/index.php' ]; then
    cp -a /app/SSPanel-Uim-dev/* /sspanel/
    cp -a /app/SSPanel-Uim-dev/config/.config.php /sspanel/config/
fi

exec "$@"