app_name="api_gateway"

default_path="/var/www/html"
#default_path="/home/jose/Descargas/new/template"
tmp_path="/tmp/$app_name"

echo '#### mkdir $tmp_path'
mkdir $tmp_path

echo '#### mv $default_path/docker $tmp_path/'
mv $default_path/docker $tmp_path/

echo '#### mv $default_path/lang/es $tmp_path/'
mv $default_path/lang/es $tmp_path/

echo '#### mv $default_path/database/migrations $tmp_path/'
mv $default_path/database/migrations $tmp_path/

echo '#### mv $default_path/database/seeders $tmp_path/'
mv $default_path/database/seeders $tmp_path/

echo '#### mv $default_path/app/Traits/ApiResponser.php $tmp_path/ApiResponser.php'
mv $default_path/app/Traits/ApiResponser.php $tmp_path/ApiResponser.php

echo '#### mv $default_path/app/Traits/ConsumeExternalService.php $tmp_path/ConsumeExternalService.php'
mv $default_path/app/Traits/ConsumeExternalService.php $tmp_path/ConsumeExternalService.php

echo '#### mv $default_path/app/Services/ServiceManager.php $tmp_path/ServiceManager.php'
mv $default_path/app/Services/ServiceManager.php $tmp_path/ServiceManager.php

echo '#### mv $default_path/app/Exceptions/Handler.php $tmp_path/Handler.php'
mv $default_path/app/Exceptions/Handler.php $tmp_path/Handler.php

echo '#### mv $default_path/app/Console/Commands/ServiceConnectionCommand.php $tmp_path/ServiceConnectionCommand.php'
mv $default_path/app/Console/Commands/ServiceConnectionCommand.php $tmp_path/ServiceConnectionCommand.php

echo '#### mv $default_path/app/Http/Requests $tmp_path'
mv $default_path/app/Http/Requests $tmp_path

echo '#### mv $default_path/config/services.php $tmp_path/services.php'
mv $default_path/config/services.php $tmp_path/services.php

echo '#### mv $default_path/config/app.php $tmp_path/app.php'
mv $default_path/config/app.php $tmp_path/app.php

echo '#### mv $default_path/dev_commands.txt $tmp_path/dev_commands.txt'
mv $default_path/dev_commands.txt $tmp_path/dev_commands.txt

echo '#### mv $default_path/instrucciones.txt $tmp_path/instrucciones.txt'
mv $default_path/instrucciones.txt $tmp_path/instrucciones.txt

echo '#### mv $default_path/install.sh $tmp_path/install.sh'
mv $default_path/install.sh $tmp_path/install.sh

echo '#### cp $default_path/uninstall.sh $tmp_path/uninstall.sh'
cp $default_path/uninstall.sh $tmp_path/uninstall.sh

echo '#### chmod +x /tmp/seller_service/uninstall.sh'
chmod +x /tmp/seller_service/uninstall.sh

echo '#### rm -r $default_path/*'
rm -r $default_path/*

echo '#### rm -r $default_path/.*'
rm -r $default_path/.*

echo '#### mv $tmp_path/* $default_path/'
mv $tmp_path/* $default_path/

