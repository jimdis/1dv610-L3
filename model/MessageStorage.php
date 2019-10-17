<?php

namespace Model;

class MessageStorage
{
    public static function getAllMessages(): array
    {
        $messageDAL = new \Model\MessageDAL();
        $messages = $messageDAL->getAllMessages();
        return $messages;
    }

    public static function getUserMessages(string $username): array
    {
        $messageDAL = new \Model\MessageDAL();
        $messages = $messageDAL->getUserMessages($username);
        return $messages;
    }

    public static function getMessageById(int $id): \Model\Message
    {
        $messageDAL = new \Model\MessageDAL();
        $message = $messageDAL->getMessageByID($id);
        return $message;
    }

    public static function storeMessage(\Model\Message $message)
    {
        $messageDAL = new \Model\MessageDAL();
        $messageDAL->storeMessage($message);
    }

    public static function updateMessage(\Model\Message $newMessage)
    {
        $messageDAL = new \Model\MessageDAL();
        $messageDAL->updateMessage($newMessage);
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
