<?php

namespace View;

class MessageTable extends View
{
    public function showAllMessages(): string
    {

        $messages = \Model\MessageStorage::getAllMessages();
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

    public function showUserMessages(): string
    {
        $username = $this->storage->getUsername();
        $tableBody = '';
        $messages = \Model\MessageStorage::getUserMessages($username);
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
