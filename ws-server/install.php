<?php
require_once 'config.php';
$pdo = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
//таблица для пользователей
$pdo->exec(
	"CREATE TABLE `chat`. `users`(
 			  		`id` INT NOT NULL AUTO_INCREMENT ,
 			  		`username` TEXT NOT NULL , 
 			  		`email` TEXT NOT NULL , 
 			  		`password_hash` TEXT NOT NULL , 
 			  		PRIMARY KEY (`id`)
 			  ) ENGINE = InnoDB;");
//таблица для чата
$pdo->exec(
	"CREATE TABLE `chat`.`messages`(
			 		`id` INT NOT NULL AUTO_INCREMENT , 
 					`author` TEXT NOT NULL , 
 					`text` TEXT NOT NULL , 
 					`time` INT NOT NULL , 
 					PRIMARY KEY (`id`)
 			  )ENGINE = InnoDB;"
);