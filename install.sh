app_name="api_gateway"

default_path="/var/www/html"
tmp_path="/tmp/$app_name"
#default_path="/home/jose/Descargas/new/api_gateway"
#tmp_path="/home/jose/Descargas/new/tmp_$app_name"

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

echo '# Eliminando modelo de usuario User.php ...'
rm $default_path/app/Models/User.php

echo '# Creando modelo para peticiones en segundo plano ...'
mv BackgroundRequest.php $default_path/app/Models/BackgroundRequest.php

echo '# Creando directorio para almacenamiento de Traits ...'
mkdir $default_path/app/Traits

echo '# Creando Trait de estandarización de respuestas ...'
mv ApiResponser.php $default_path/app/Traits/ApiResponser.php

echo '# Creando Trait para consumir servicios desde el API GATEWAY ...'
mv ServiceConsumer.php $default_path/app/Traits/ServiceConsumer.php

echo '# Creando Trait para serializar requests ...'
mv JsonRequestConverter.php $default_path/app/Traits/JsonRequestConverter.php

echo '# Creando directorio para almacenamiento de servicios ...'
mkdir $default_path/app/Services

echo '# Creando clase base para nuevas clases de administración y/o conexión a servicios'
mv ServiceManager.php $default_path/app/Services/ServiceManager.php

echo '# Creando directorio para almacenamiento de comandos ...'
mkdir $default_path/app/Console/Commands

echo '# Creando comando para la generación de recursos y configuraciones de conexión a servicios ...'
mv ServiceConnectionCommand.php $default_path/app/Console/Commands/ServiceConnectionCommand.php

echo '# Creando middleware de autenticación de solicitudes al API GATEWAY ...'
mv AuthenticateAccessMiddleware.php $default_path/app/Http/Middleware/AuthenticateAccessMiddleware.php

echo '# Creando manejador de excepciones del sistema ...'
mv Handler.php $default_path/app/Exceptions/Handler.php

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

echo '# Creación de migración para tabla de peticiones en segundo plano ...'
rm -r $default_path/database/migrations
mv $default_path/migrations/ $default_path/database/

echo '# Creando StubFormatter.php para crear clases de objetos laravel dinámicamente ...'
mv $default_path/Helpers $default_path/app/

echo '# Asignando permisos de edición al proyecto'
sh ./docker/commands/dev_files_permissions.sh
sh ./docker/commands/dev_dir_permissions.sh

echo '# Api Gateway instalado con éxito. Realice las siguientes configuraciones para terminar.'
echo ''
echo '1. Agregue el valor \App\Http\Middleware\AuthenticateAccessMiddleware::class en app\Http\Kernel.php en la variable $middleware'
echo '2. Agregue la clave access_secrets con el valor env("ACCESS_SECRETS") en config\services.php'
echo '3. Configure su archivo .env'
echo '4. Ejecute php artisan migrate en el contenedor o artisan migrate si configuró comandos para acceso añ contenedor'