<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.10.2018
 * Time: 12:28
 */

namespace chat;

/**
 * Class ServerMessage
 * @package ws
 */
class ServerMessage
{
    const ONLINE_EVENT = 'onlineEvent';
    const ATTACK_EVENT = 'attackEvent';
    const EXPIRED_EVENT = 'expiredEvent';
    const USERNAME_EVENT = 'usernameEvent';
    const LAST_MESSAGES_EVENT = 'lastMessagesEvent';
    const NEW_MESSAGE_EVENT = 'newMessageEvent';

    public $type;
    public $data;

    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function __toString()
    {
        return json_encode($this);
    }

}