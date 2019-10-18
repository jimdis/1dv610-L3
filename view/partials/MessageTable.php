<?php

namespace View;

class MessageTable
{
    private static $delete = __CLASS__ . '::deleteMessage';

    public static function userWantsToDeleteMessage(): bool
    {
        return isset($_POST[self::$delete]);
    }

    public static function getMessageId(): int
    {
        return $_POST[self::$delete];
    }


    public static function showAllMessages(): string
    {

        $messages = \Model\MessageDAL::getAllMessages();
        $tableBody = '
        <thead>
            <tr>
            <th>Author</th>
            <th>Message</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($messages as $message) {
            $author = $message->author;
            $content = $message->content;
            $tableBody .= '
            <tr>
            <td>' . $author . '</td>
            <td>' . $content . '</td>
            </tr>';
        }
        return '
        <table>'
            . $tableBody . '
        </tbody>
        </table>';
    }

    public static function showUserMessages(string $username): string
    {
        $tableBody = '';
        $messages = \Model\MessageDAL::getUserMessages($username);
        foreach ($messages as $message) {
            $id = $message->id;
            $content = $message->content;
            $tableBody .= '
            <tr>
            <td>' . $content . '</td>
            <td>
                <form action="">
                    <input hidden name="messages"/>    
                    <input hidden name="edit" value="' . $id . '"/>
                    <button type="submit">Edit</button>
                </form>
            </td>
            <td>
                <form method="post" action="?messages">
                    <input hidden name="messages"/>    
                    <input hidden name="' . self::$delete . '" value="' . $id . '"/>
                    <button type="submit">Delete</button>
                </form>
            </td>
            </tr>';
        }
        return '<table>
        <thead>
        <tr>
        <th>Message</th>
        <th>Edit<th>
        <th>Delete</th>
        </tr>
        </thead>
        <tbody>'
            . $tableBody . '
        </tbody>
        </table>';
    }
}
