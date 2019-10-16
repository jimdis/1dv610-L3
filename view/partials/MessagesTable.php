<?php

namespace View;

class MessagesTable
{
    //Todo: handle empty list..
    public static function generateMessagesTableHTML(string $username = null): string
    {
        $messages = $username == null ? \Model\MessageStorage::getAllMessages() : \Model\MessageStorage::getUserMessages($username);
        $editColumn = $username == null ? '' : '<th>Edit</th>';
        $messagesHTML = '
        <thead>
            <tr>
            <th>Author</th>
            <th>Message</th>
            ' . $editColumn . '
            </tr>
        </thead>
        <tbody>';
        foreach ($messages as $message) {
            $id = $message->id;
            $editButton = $username == null ? '' : '<td>
                <form action="">
                    <input hidden name="messages"/>    
                    <input hidden name="edit" value="' . $id . '"/>
                    <button type="submit">Update</button>
                </form>
            </td>';
            $author = $message->author;
            $content = $message->content;
            $messagesHTML .= '
            <tr>
            <td>' . $author . '</td>
            <td>' . $content . '</td>
            '    . $editButton . '
            </tr>';
        }
        return '
        <table>'
            . $messagesHTML . '
        </tbody>
        </table>';
    }
}
