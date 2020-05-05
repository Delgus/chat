<?php
$pdo = new PDO(getenv("DB_DSN"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));

$pdo->exec(
    "CREATE TABLE `messages`(
			 		`id` INT NOT NULL AUTO_INCREMENT , 
 					`author` TEXT NOT NULL , 
 					`text` TEXT NOT NULL , 
 					`time` INT NOT NULL , 
 				PRIMARY KEY (`id`));"
);

$pdo->exec(
    "CREATE TABLE `users`(
 			  		`id` INT NOT NULL AUTO_INCREMENT ,
 			  		`username` TEXT NOT NULL , 
 			  		`email` TEXT NOT NULL , 
 			  		`password_hash` TEXT NOT NULL , 
 			  		PRIMARY KEY (`id`));");