symfony new sapio --full
symfony server:start

composer require symfony/security-bundle

php bin/console make:user

php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Login

composer require symfonycasts/verify-email-bundle
php bin/console make:registration-form
php bin/console make:auth
