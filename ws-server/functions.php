<?php
/**
 * @return string
 */
function getMessages()
{
    $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $query = $pdo->query("SELECT * FROM messages");
    $messages = $query->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return json_encode($messages);
}

/**
 * @param $user
 * @param $message
 */
function writeMessage($user, $message)
{
    $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $query = $pdo->prepare("INSERT INTO messages VALUES(0,:author,:text,:time)");
    $query->execute([':author' => $user, ':text' => $message, ':time' => time()]);
    $pdo = null;
}