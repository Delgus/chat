<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 17.10.2018
 * Time: 10:46
 */

namespace db;

use PDO;

/**
 * Class Db
 * @package db
 */
class Db
{
    /**
     * @var PDO
     */
    private $pdo;

    private $_dsn;
    private $_username;
    private $_password;

    public function __construct($dsn, $username, $password)
    {
        $this->_dsn = $dsn;
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * Получить сообщения
     * @return array
     */
    public function getMessages()
    {
        $this->open();
        $messages = $this->pdo->query(
            "SELECT * FROM (SELECT * FROM `messages` ORDER BY id DESC LIMIT " . MESSAGES_ON_PAGE . ") AS _t ORDER BY id ASC;"
        )->fetchAll(PDO::FETCH_ASSOC);
        $this->close();
        return $messages;
    }

    /**
     * Записать сообщение
     * @param $user
     * @param $message
     */
    public function writeMessage($user, $message)
    {
        $this->open();
        $query = $this->pdo->prepare("INSERT INTO messages VALUES(0,:author,:text,:time)");
        $query->execute([':author' => $user, ':text' => $message, ':time' => time()]);
        $this->close();
    }

    /**
     * Сохранение данных пользователя в БД
     * @param $username
     * @param $email
     * @param $password_hash
     */
    public function saveUser($username, $email, $password_hash)
    {
        $this->open();
        $query = $this->pdo->prepare("INSERT INTO users VALUES(0,:username,:email,:password_hash)");
        $query->execute([':username' => $username, ':email' => $email, ':password_hash' => $password_hash]);
        $this->close();
    }

    /**
     * @param $username
     * @return bool|mixed
     */
    public function getUserByUsername($username)
    {
        $this->open();
        $query = $this->pdo->prepare("SELECT * FROM users WHERE username=:username");
        $result = false;
        if ($query->execute([':username' => $username])) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
        }
        $this->close();
        return $result;
    }

    /**
     * @param $email
     * @return bool|mixed
     */
    public function getUserByEmail($email)
    {
        $this->open();
        $query = $this->pdo->prepare("SELECT * FROM users WHERE email=:email");
        $result = false;
        if ($query->execute([':email' => $email])) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
        }
        $this->close();
        return $result;
    }

    /**
     * @return $this
     */
    private function open()
    {
        $this->pdo = new PDO($this->_dsn, $this->_username, $this->_password);
        return $this;
    }

    /**
     * @return $this
     */
    private function close()
    {
        $this->pdo = null;
        return $this;
    }
}