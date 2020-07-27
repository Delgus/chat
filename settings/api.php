<?php
require_once __DIR__ . '/../vendor/autoload.php';

// read environments
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

echo json_encode([
    "ws" => $_ENV["WS_ADDR"]
]);
