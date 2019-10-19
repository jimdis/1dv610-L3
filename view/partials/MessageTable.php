<?php

namespace View;

class MessageTable
{
    private static $delete = __CLASS__ . '::deleteMessage';
    private static $edit = __CLASS__ . '::editMessage';

    public static function userWantsToDeleteMessage(): bool
    {
        return isset($_POST[self::$delete]);
    }

    public static function userWantsToEditMessage(): bool
    {
        return isset($_POST[self::$edit]);
    }

    public static function getMessageId(): int
    {
        return $_POST[self::$delete] ?? $_POST[self::$edit];
    }


    public static function showAllMessages(): string
    {

        $messages = \Model\MessageDAL::getAllMessages();
        $tableBody = '
        <thead>
            <tr>
            <th>Date</th>
            <th>Author</th>
            <th>Message</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($messages as $message) {
            $date = $message->updated;
            $author = $message->author;
            $content = $message->content;
            $tableBody .= '
            <tr>
            <td>' . $date . '</td>
            <td>' . $author . '</td>
            <td>' . $content . '</td>
            </tr>';
        }
        return count($messages) == 0 ? 'There are currently no messages..' : '
        <table>'
            . $tableBody . '
        </tbody>
        </table>';
    }

    public static function showUserMessages(string $username): string
    {
        $tableBody = '
        <thead>
        <tr>
        <th>Date</th>
        <th>Message</th>
        <th>Edit</th>
        <th>Delete</th>
        </tr>
        </thead>
        <tbody>';
        $messages = \Model\MessageDAL::getUserMessages($username);
        foreach ($messages as $message) {
            $date = $message->updated;
            $id = $message->id;
            $content = $message->content;
            $tableBody .= '
            <tr>
            <td>' . $date . '</td>
            <td>' . $content . '</td>
            <td>
                <form method="post" action="?' . \Model\Routes::$messages . '">
                    <input hidden name="' . self::$edit . '" value="' . $id . '"/>
                    <button type="submit">Edit</button>
                </form>
            </td>
            <td>
                <form method="post" action="?' . \Model\Routes::$messages . '">
                    <input hidden name="' . self::$delete . '" value="' . $id . '"/>
                    <button type="submit">Delete</button>
                </form>
            </td>
            </tr>';
        }
        return count($messages) == 0 ? 'There are currently no messages..' : '
        <table>'
            . $tableBody . '
        </tbody>
        </table>';
    }
}
