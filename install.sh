app_name="crm_api_gateway"

default_path="/var/www/html"
tmp_path="/tmp/$app_name"
#default_path="/home/jose/Descargas/new/api_gateway"
#tmp_path="/home/jose/Descargas/new/tmp_$app_name"

#!/bin/bash

REDIS=0
# Definir los argumentos
while [[ $# -gt 0 ]]
do
key="$1"
case $key in
    -r|--redis)
    REDIS=1
    shift
    shift
    ;;
    #-o|--output)
    #OUTPUT="$2"
    #shift
    #shift
    #;;
    *)
    shift
    ;;
esac
done

echo "shopt -s dotglob"
shopt -s dotglob

echo '# Creando proyecto en ruta temporal ...'
composer create-project laravel/laravel $tmp_path

echo '# Moviendo archivos ocultos a ruta final $default_path ...'
mv $tmp_path/.* $default_path/

echo '# Moviendo archivos y directorios a ruta final $default_path ...'
mv $tmp_path/* $default_path/

echo '# Eliminando ruta temporal ...'
rm -r $tmp_path/

echo '# Creando middlewares ...'
mv $default_path/Middleware $default_path/app/Http

echo '# Eliminando y remplazando modelos ...'
rm -r $default_path/app/Models
mv $default_path/Models/ $default_path/app/

echo '# Creando Traits ...'
mkdir $default_path/app/Traits
mv $default_path/Traits/ $default_path/app/

echo '# Creando directorio para almacenamiento de servicios ...'
mkdir $default_path/app/Services

echo '# Creando directorio para almacenamiento de comandos ...'
mkdir $default_path/app/Console/Commands

echo '# Creando comando para la generación de recursos y configuraciones de conexión a servicios ...'
mv ServiceConnectionCommand.php $default_path/app/Console/Commands/ServiceConnectionCommand.php

echo '# Creando manejador de excepciones del sistema ...'
mv Handler.php $default_path/app/Exceptions/Handler.php

echo '# Creando controlador de autenticación ...'
mv AuthenticationController.php $default_path/app/Http/Controllers/AuthenticationController.php

echo '# Publicando archivos de internacionalización ...'
php artisan lang:publish

echo '# Creando archivos de internacionalización para español ...'
mv $default_path/es $default_path/lang/

echo '# Instalando laravel octane para mejorar el rendimiento de la aplicación ...'
composer require laravel/octane --with-all-dependencies
# Realiza la instalación de laravel octane con las respuestas de consola necesarias
printf '0\nyes' | php artisan octane:install
# Permisos de ejecución para el archivo rr
chmod +x $default_path/rr

echo '# Creación de migraciones requeridas ...'
rm -r $default_path/database/migrations
mv $default_path/migrations/ $default_path/database/

echo '# Creación de seeders ...'
rm -r $default_path/database/seeders
mv $default_path/seeders/ $default_path/database/
echo '# Creando StubFormatter.php para crear clases de objetos laravel dinámicamente ...'
mv $default_path/Helpers $default_path/app/

echo '# Instalando laravel sanctum'
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

echo '# Instalando laravel-permission para control de role sy permisos'
composer require spatie/laravel-permission

if [ "$REDIS" -eq 1 ]
then
    echo '# Insalando redis'
    composer require predis/predis
fi

echo '# Instalando rutas iniciales ...'
rm $default_path/routes/api.php
mv api.php $default_path/routes/api.php

echo '# Asignando permisos de edición al proyecto'
sh ./docker/commands/dev_files_permissions.sh
sh ./docker/commands/dev_dir_permissions.sh

echo '# Api Gateway instalado con éxito. Realice las siguientes configuraciones para terminar.'
echo ''
echo '* Agregue los middlewares de laravel permission en el archivo app\Http\Kernel.php en la variable $middlewareAliases'
echo '  "role" => \Spatie\Permission\Middlewares\RoleMiddleware::class,'
echo '  "permission" => \Spatie\Permission\Middlewares\PermissionMiddleware::class,'
echo '  "role_or_permission" => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,'
echo '* Agregue el middleware de autenticación de usuario de otros servicios en el archivo app\Http\Kernel.php en la variable $middlewareAliases'
echo '  "auth_service_user" => \App\Http\Middleware\AuthenticateServiceUser::class,'
echo '* Si va a utilizar autenticación para un SPA debe habilitar o agregar el siguiente middleware en la clave api del archivo app\Http\Kernel.php: \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,'
echo '* Configure el modelo User.php y su respectiva migración si requiere campos adicionales en la tabla de usuarios'
echo '* Configure su archivo .env'
echo '* En la migración de personal access tokens cambie $table->morphs("tokenable"); por $table->uuidMorphs("tokenable");'
echo '* Ejecute php artisan migrate en el contenedor o artisan migrate si configuró comandos para acceso al contenedor'
echo '* Configure el Sedder de roles y permisos RolesAndPermissionsSeeder.php de acuerdo a los módulos y privilegios de su sistema'
echo '* Ejecute php artisan db:seed en el contenedor o artisan db:seed si configuró comandos para acceso al contenedor'

if [ "$REDIS" -eq 1 ]
then
    echo '* Configure su archivo .env para la conexión con redis (datos de acceso, host, puerto, cliente. Si va a manejar sesión, cache, etc)'
fi
