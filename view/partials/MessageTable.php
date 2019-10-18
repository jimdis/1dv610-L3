<?php

namespace View;

class MessageTable
{
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
            </tr>';
        }
        return '<table>
        <thead>
        <tr>
        <th>Message</th>
        <th>Edit<th>
        </tr>
        </thead>
        <tbody>'
            . $tableBody . '
        </tbody>
        </table>';
    }
}
