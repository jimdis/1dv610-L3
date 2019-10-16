<?php

namespace Model;

class MessageDAL extends Database
{
    public function getAllMessages(): array
    {
        $sql = "SELECT * FROM message";
        $query = $this->connect()->prepare($sql);
        $query->execute();
        $foundMessages = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($foundMessages) {
            $messages = [];
            foreach ($foundMessages as $foundMessage) {
                $author = $foundMessage['author'];
                $content = $foundMessage['content'];
                $id = $foundMessage['id'];
                $message = new \Model\Message($author, $content, $id);
                array_push($messages, $message);
            }
            return $messages;
        } else {
            return [];
        }
    }

    public function getUserMessages(string $username): array
    {
        $sql = "SELECT * FROM message WHERE author = ?";
        $query = $this->connect()->prepare($sql);
        $query->execute([$username]);
        $foundMessages = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($foundMessages) {
            $messages = [];
            foreach ($foundMessages as $foundMessage) {
                $author = $foundMessage['author'];
                $content = $foundMessage['content'];
                $id = $foundMessage['id'];
                $message = new \Model\Message($author, $content, $id);
                array_push($messages, $message);
            }
            return $messages;
        } else {
            return [];
        }
    }

    public function getMessageByID(int $id): \Model\Message
    {
        $sql = "SELECT * FROM message WHERE id = ?";
        $query = $this->connect()->prepare($sql);
        $query->execute([$id]);
        $foundMessage = $query->fetch(\PDO::FETCH_ASSOC);
        if ($foundMessage) {
            $author = $foundMessage['author'];
            $content = $foundMessage['content'];
            $id = $foundMessage['id'];
            $message = new \Model\Message($author, $content, $id);
            return $message;
        } else {
            throw new \Model\IncorrectCredentialsException();
        }
    }

    public function updateMessage(\Model\Message $newMessage): void
    {
        $sql = "UPDATE message SET content = ? WHERE id = ? AND author = ?";
        $query = $this->connect()->prepare($sql);
        $query->execute([$newMessage->content, $newMessage->id, $newMessage->author]);
        if ($query->rowCount() < 1) {
            throw new \Exception('No records updated. Username did not match');
        }
    }

    public function storeMessage(\Model\Message $message): void
    {
        $sql = "INSERT INTO message (author, content, isVerified) VALUES (?, ?, ?)";
        $query = $this->connect()->prepare($sql);
        $query->execute([$message->author, $message->content, $message->isVerified]);
    }

    public function storeToken(\Model\Token $token, string $username)
    {
        $expires = $this->getFormattedDate($token->getExpires());
        $userId = $this->getUserId($username);
        $sql = "INSERT INTO tokens (userid, content, expires) VALUES (?, ?, ?)";
        $query = $this->connect()->prepare($sql);
        $success = $query->execute([$userId, $token->getContent(), $expires]);
        if (!$success) {
            $this->handlePDOError($query->errorInfo());
        }
    }
}
