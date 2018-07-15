php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
chmod -R 777 var/cache/*