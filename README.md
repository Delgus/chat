# chat
Simple example for chat use websocket

This is a project for my training. Don't use it in production.

![screenshot](https://delgus.github.io/img/screenshot.png)
## Install  
```
git clone  https://github.com/Delgus/chat.git  
cd chat  
composer install  
```

## Configuration
```shell script
cp .env.example .env
```

Create tables in db  
```shell script
php init-db.php
```

## Running

To start the server web socket as daemon  
```shell script
php ws-server.php start -d  
```

You need to configure the web server so  that your server has activated 
support for PHP and that all files ending in .php are handled by PHP.

You can also use the php built-in server for the test 
```
php -S 127.0.0.1:8080
```

and see your application on http://127.0.0.1:8080


