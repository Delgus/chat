<?php
require_once __DIR__.'/../../config/config-local.php';
$pdo = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
//create users table
$pdo->exec(
	"CREATE TABLE `users`(
 			  		`id` INT NOT NULL AUTO_INCREMENT ,
 			  		`username` TEXT NOT NULL , 
 			  		`email` TEXT NOT NULL , 
 			  		`password_hash` TEXT NOT NULL , 
 			  		PRIMARY KEY (`id`)
 			  ) ENGINE = InnoDB;");