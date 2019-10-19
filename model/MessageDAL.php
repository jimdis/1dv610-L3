<?php

namespace Model;

class MessageDAL
{
    public static function getAllMessages(): array
    {
        $sql = "SELECT * FROM message ORDER BY updated DESC";
        $query = self::connect()->prepare($sql);
        $query->execute();
        $foundMessages = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($foundMessages) {
            return self::mapMessageArray($foundMessages);
        } else {
            return [];
        }
    }

    public static function getUserMessages(string $username): array
    {
        $sql = "SELECT * FROM message WHERE author = ? ORDER BY updated DESC";
        $query = self::connect()->prepare($sql);
        $query->execute([$username]);
        $foundMessages = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($foundMessages) {
            return self::mapMessageArray($foundMessages);
        } else {
            return [];
        }
    }

    public static function getMessageByID(int $id): \Model\Message
    {
        $sql = "SELECT * FROM message WHERE id = ?";
        $query = self::connect()->prepare($sql);
        $query->execute([$id]);
        $foundMessage = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundMessage) {
            return self::mapMessage($foundMessage);
        } else {
            throw new \Exception('Message not found');
        }
    }

    public static function updateMessage(\Model\Message $newMessage): void
    {
        $sql = "UPDATE message SET content = ? WHERE id = ? AND author = ?";
        $query = self::connect()->prepare($sql);
        $query->execute([$newMessage->content, $newMessage->id, $newMessage->author]);
        if ($query->rowCount() < 1) {
            throw new \Exception('No records updated. Username did not match');
        }
    }

    public static function storeMessage(\Model\Message $message): void
    {
        $sql = "INSERT INTO message (author, content, isVerified) VALUES (?, ?, ?)";
        $query = self::connect()->prepare($sql);
        $query->execute([$message->author, $message->content, $message->isVerified == true ? 1 : 0]);
        if ($query->rowCount() < 1) {
            throw new \Exception('Error: No records updated.');
        }
    }

    public static function deleteMessage(int $id): void
    {
        $sql = "DELETE FROM message WHERE id = ?";
        $query = self::connect()->prepare($sql);
        $query->execute([$id]);
        if ($query->rowCount() < 1) {
            throw new \Exception('Error: No records updated.');
        }
    }

    public static function validateAuthor(string $username)
    {
        try {
            \Model\UserDAL::getUser($username);
            throw new \Exception('Username already exists. Pick another!');
        } catch (\Model\IncorrectCredentialsException $e) {
            return;
        }
    }

    private static function mapMessageArray(array $queryResults): array
    {
        $messages = [];
        foreach ($queryResults as $queryResult) {
            $message = self::mapMessage($queryResult);
            array_push($messages, $message);
        }
        return $messages;
    }

    private static function mapMessage(array $queryResult): \Model\Message
    {
        $author = $queryResult['author'];
        $content = $queryResult['content'];
        $id = $queryResult['id'];
        $updated = $queryResult['updated'];
        $message = new \Model\Message($author, $content, $id, $updated);
        return $message;
    }

    private static function connect(): \PDO
    {
        $db = new \Model\Database();
        return $db->connect();
    }
}
