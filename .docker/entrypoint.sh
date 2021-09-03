#!/bin/bash
set -e

cd /var/www
composer dump-autoload
php src/start.php

exec "$@"
