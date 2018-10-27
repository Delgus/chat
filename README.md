# chat
Simple example for chat use websocket

This is a project for my training. Don't use it in production.

![screenshot](https://delgus.tk/img/screenshot.png)
## Install  
```
git clone  https://github.com/Delgus/chat.git  
cd chat  
composer install  
```

Create file for configure application:
```
php init.php
```


You must specify your settings in config/config-local.php

Create tables in db  
```
php auth/bin/auth-install.php
php chat/bin/chat-install.php
```  
or
```
composer auth-install
composer chat-install
```



To start the server web socket as daemon  
```
php chat/bin/server.php start -d  
```
or
```
composer chat-server-start
```

To stop the server web socket
```
php chat/bin/server.php stop 
```
or
```
composer chat-server-stop
```

You need to configure the web server so  that your server has activated 
support for PHP and that all files ending in .php are handled by PHP.

You can also use the php built-in server for the test 
```
php -S 127.0.0.1:8000
```

and see your application on http://127.0.0.1:8000
