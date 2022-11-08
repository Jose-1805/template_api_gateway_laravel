app_name="api_gateway"
default_path="/var/www/html"
#default_path="/home/jose/Descargas/new/template"
tmp_path="/tmp/$app_name"

echo "shopt -s dotglob"
shopt -s dotglob

echo '### find $default_path/ -type f -exec chmod 0777 {} \;'
find $default_path/ -type f -exec chmod 0777 {} \;

echo '### find $default_path/ -type d -exec chmod 0777 {} \;'
find $default_path/ -type d -exec chmod 0777 {} \;

echo '### composer create-project laravel/laravel:^9.0 /tmp/$app_name'
composer create-project laravel/laravel /tmp/$app_name

echo '### mv $tmp_path/.* $default_path/'
mv $tmp_path/.* $default_path/

echo '### mv $tmp_path/* $default_path/'
mv $tmp_path/* $default_path/

echo '### rm -r $tmp_path/'
rm -r $tmp_path/

echo '### rm -r $default_path/.git'
rm -r $default_path/.git

echo '### mkdir $default_path/app/Traits'
mkdir $default_path/app/Traits

echo '### mkdir $default_path/app/Services'
mkdir $default_path/app/Services

echo '### mkdir $default_path/app/Console/Commands'
mkdir $default_path/app/Console/Commands

echo '### mv ApiResponser.php $default_path/app/Traits/ApiResponser.php'
mv ApiResponser.php $default_path/app/Traits/ApiResponser.php

echo '### mv ConsumeExternalService.php $default_path/app/Traits/ConsumeExternalService.php'
mv ConsumeExternalService.php $default_path/app/Traits/ConsumeExternalService.php

echo '### mv ServiceManager.php $default_path/app/Services/ServiceManager.php'
mv ServiceManager.php $default_path/app/Services/ServiceManager.php

echo '### mv Handler.php $default_path/app/Exceptions/Handler.php'
mv Handler.php $default_path/app/Exceptions/Handler.php

echo '### mv ServiceConnectionCommand.php $default_path/app/Console/Commands/ServiceConnectionCommand.php'
mv ServiceConnectionCommand.php $default_path/app/Console/Commands/ServiceConnectionCommand.php

echo '### mv $default_path/es/ $default_path/lang/'
mv $default_path/es/ $default_path/lang/

echo '### mv $default_path/Requests/ $default_path/app/Http/'
mv $default_path/Requests/ $default_path/app/Http/

echo '### mv services.php $default_path/config/services.php'
mv services.php $default_path/config/services.php

echo '### mv $default_path/app.php $default_path/config/app.php'
mv $default_path/app.php $default_path/config/app.php

echo '### composer require laravel/octane'
composer require laravel/octane

echo '### printf '0\nyes' | php artisan octane:install'
printf '0\nyes' | php artisan octane:install

echo '### chmod +x $default_path/rr'
chmod +x $default_path/rr

echo '### composer require laravel/fortify'
composer require laravel/fortify

echo '### php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"'
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"

echo '### composer require spatie/laravel-permission'
composer require spatie/laravel-permission

echo '### php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"'
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

echo '### rm -r $default_path/database/migrations'
rm -r $default_path/database/migrations

echo '### mv $default_path/migrations/ $default_path/database/'
mv $default_path/migrations/ $default_path/database/

echo '### rm -r $default_path/database/seeders'
rm -r $default_path/database/seeders

echo '### mv $default_path/seeders/ $default_path/database/'
mv $default_path/seeders/ $default_path/database/

echo '### find $default_path/ -type f -exec chmod 0777 {} \;'
find $default_path/ -type f -exec chmod 0777 {} \;

echo '### find $default_path/ -type d -exec chmod 0777 {} \;'
find $default_path/ -type d -exec chmod 0777 {} \;

echo '#################################################################'
echo 'Api Gateway instalado con éxito, configure su archivo .env y ejecute >> php artisan migrate'
