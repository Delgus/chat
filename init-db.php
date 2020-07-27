<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO(
    $_ENV["DB_DSN"],
    $_ENV["DB_USERNAME"],
    $_ENV["DB_PASSWORD"]
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS `messages`(
			 		`id` INT NOT NULL AUTO_INCREMENT , 
 					`author` TEXT NOT NULL , 
 					`text` TEXT NOT NULL , 
 					`time` INT NOT NULL , 
 				PRIMARY KEY (`id`));"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS `users`(
 			  		`id` INT NOT NULL AUTO_INCREMENT ,
 			  		`username` TEXT NOT NULL , 
 			  		`email` TEXT NOT NULL , 
 			  		`password_hash` TEXT NOT NULL , 
 			  		PRIMARY KEY (`id`));"
);
