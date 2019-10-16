<?php

namespace Model;

class MessageStorage
{
    private static $messages = [];

    public static function getAllMessages(): array
    {
        array_push(self::$messages, new \Model\Message('Jim', 'Hello World!')); // TODO: Testing only - remove
        return self::$messages;
    }

    public static function getUserMessages(string $username): array
    {
        $message = new \Model\Message('Username', 'Hello World!', 2);
        array_push(self::$messages, $message);
        return self::$messages;
    }

    public static function getMessageById(int $id): \Model\Message
    {
        $message = new \Model\Message('Username', 'Hello World!', $id);
        return $message;
    }

    public static function storeNewMessage(\Model\Message $message)
    {
        array_push(self::$messages, $message);
    }

    //TODO: A bit inverted logic..
    public static function validateAuthor(string $username)
    {
        try {
            $userDAL = new \Model\UserDAL();
            $userDAL->getUser($username);
            throw new \Exception('Username already exists. Pick another!');
        } catch (\Model\IncorrectCredentialsException $e) {
            return;
        }
    }
}
