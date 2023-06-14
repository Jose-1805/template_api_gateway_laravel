app_name="service_api_gateway"

default_path="/var/www/html"
tmp_path="/tmp/$app_name"

echo '# Creando directorio temporal ...'
mkdir $tmp_path

echo '# Moviendo archivos y directorios ...'
mv $default_path/docker $tmp_path/
mkdir $tmp_path/Middleware
mv $default_path/app/Http/Middleware/AuthenticateServiceUser.php $tmp_path/Middleware
mv $default_path/lang/es $tmp_path/
mv $default_path/app/Helpers $tmp_path/
mv $default_path/stubs $tmp_path/
mv $default_path/database/migrations $tmp_path/
mv $default_path/database/seeders $tmp_path/
mv $default_path/app/Console/Commands $tmp_path/
mv $default_path/app/Background $tmp_path/
mv $default_path/config/background.php $tmp_path/background.php
mv $default_path/app/Exceptions/Handler.php $tmp_path/Handler.php
mv $default_path/app/Http/Controllers/AuthenticationController.php $tmp_path/AuthenticationController.php
mv $default_path/routes/api.php $tmp_path/api.php
mv $default_path/app/Models/ $tmp_path/
mv $default_path/app/Traits/ $tmp_path/
mv $default_path/config/amqp.php $tmp_path/amqp.php
mv $default_path/dev_commands.sh $tmp_path/dev_commands.sh
mv $default_path/instrucciones.txt $tmp_path/instrucciones.txt
mv $default_path/install.sh $tmp_path/install.sh
mv $default_path/uninstall.sh $tmp_path/uninstall.sh

echo '# Limpiando directorio por defecto ...'
rm -r $default_path/*
#rm -r $default_path/.*

echo '# Moviendo archivos al directorio final ...'
mv $tmp_path/* $default_path/

echo 'Desinstalación terminada con éxito.'

