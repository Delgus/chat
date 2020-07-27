<?php
namespace db;

use PDO;

/**
 * Class Db
 * @package db
 */
class DB
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    public function __construct(string $dsn, string $username, string $password)
    {
        $this->pdo = new PDO($dsn, $username, $password);
    }

    /**
     * Получить сообщения
     *
     * @param int $limit
     * @param string $date_format
     * @return array
     */
    public function getMessages(int $limit, string $date_format): array
    {
        $query = $this->pdo->query("SELECT * FROM `messages` ORDER BY id DESC LIMIT " . $limit);
        $data = [];
        if ($query) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as &$one) {
                $one['time'] = date($date_format, $one['time']);
            }
        }

        return $data;
    }

    /**
     * Записать сообщение
     *
     * @param $user
     * @param $message
     * @return bool
     */
    public function writeMessage($user, $message): bool
    {
        $query = $this->pdo->prepare("INSERT INTO messages VALUES(0,:author,:text,:time)");
        if ($query->execute([':author' => $user, ':text' => $message, ':time' => time()])) {
            return true;
        }
        return false;
    }

    /**
     * Сохранение данных пользователя в БД
     *
     * @param $username
     * @param $email
     * @param $password_hash
     * @return bool
     */
    public function saveUser($username, $email, $password_hash): bool
    {
        $query = $this->pdo->prepare("INSERT INTO users VALUES(0,:username,:email,:password_hash)");
        if ($query->execute([':username' => $username, ':email' => $email, ':password_hash' => $password_hash])) {
            return true;
        }
        return false;
    }

    /**
     * Получаем пользователя по имени
     * @param $username
     * @return mixed|null
     */
    public function getUserByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username=:username");
        if ($stmt->execute([':username' => $username])) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }

    /**
     * @param $email
     *
     * @return bool|mixed
     */
    public function getUserByEmail($email)
    {
        $query = $this->pdo->prepare("SELECT * FROM users WHERE email=:email");
        if ($query->execute([':email' => $email])) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }
}
