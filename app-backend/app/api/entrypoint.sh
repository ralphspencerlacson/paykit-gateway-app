#!/bin/sh

# Run Composer install
composer install

# Start the cron service
crond

# Execute the main process (php-fpm or any other command passed to the container)
exec "$@"
