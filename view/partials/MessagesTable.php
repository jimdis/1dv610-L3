<?php

namespace View;

class MessagesTable
{
    public static function generateMessagesTableHTML(): string
    {
        $messages = \Model\MessageStorage::getMessages();
        $messagesHTML = '<tr><th>Author</th><th>Message</th></tr>';
        foreach ($messages as $message) {
            $author = $message->getAuthor();
            $content = $message->getContent();
            $messagesHTML .= "<tr><td>$author</td><td>$content</td></tr>";
        }
        return
            "<table>$messagesHTML</table>
            ";
    }
}
