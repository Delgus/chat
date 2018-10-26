<?php
require_once __DIR__ . '/../../config/config-local.php';
$pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
//create chat messages table
$pdo->exec(
    "CREATE TABLE `messages`(
			 		`id` INT NOT NULL AUTO_INCREMENT , 
 					`author` TEXT NOT NULL , 
 					`text` TEXT NOT NULL , 
 					`time` INT NOT NULL , 
 					PRIMARY KEY (`id`)
 			  )ENGINE = InnoDB;"
);