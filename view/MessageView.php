<?php

namespace View;

class MessageView extends View
{
    private static $submitMessage = __CLASS__ . '::submitMessage';
    private static $updateMessage = __CLASS__ . '::updateMessage';
    private static $updateMessageId = __CLASS__ . '::updateMessageId';
    private static $name = __CLASS__ . '::UserName';
    private static $messageContent = __CLASS__ . '::messageContent';
    private static $messageId = __CLASS__ . '::Message';
    private static $editParam = 'edit';
    private $username;
    private $isAuthorizedEditor = true;

    public function getNewMessage(): \Model\Message
    {
        $author = $_POST[self::$name] ?? '';
        $content = $_POST[self::$messageContent] ?? '';
        $id = $_POST[self::$updateMessageId] ?? null;
        return new \Model\Message($author, $content, $id);
    }

    public function newMessageSubmitted(): bool
    {
        return isset($_POST[self::$submitMessage]);
    }

    public function messageUpdateSubmitted(): bool
    {
        return isset($_POST[self::$updateMessage]);
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function showEditMode(): bool
    {
        if (isset($_POST[self::$updateMessage])) {
            return false;
        } else if (isset($_GET[self::$editParam])) {
            return true;
        } else return false;
    }

    public function getMessageId(): int
    {
        return $_GET[self::$editParam];
    }

    public function setIsAuthorizedEditor(bool $bool)
    {
        $this->isAuthorizedEditor = $bool;
    }


    public function show(): string
    {
        $form = $this->showEditMode() ? $this->generateMessageEditFormHTML($this->message) : $this->generateMessageFormHTML($this->message);
        $html = $form . '<br/><h2>Message Board</h2>' . \View\MessageTable::showAllMessages();
        return $html;
    }

    private function generateMessageFormHTML(): string
    {
        $isLoggedIn = $this->storage->getUserIsAuthenticated();
        $linkText = $isLoggedIn ? 'Account' : 'Go to login';
        $username = "<strong>$this->username</strong>";
        $hidden = $isLoggedIn ? 'hidden' : '';
        return '
        <a href=".">' . $linkText . '</a><br /><br />
            <form method="post" action="?messages">
				<fieldset>
					<legend>Write a new message</legend>
					<p id="' . self::$messageId . '">' . $this->message . '</p>
                    
                    <label for="' . self::$name . '">Username :</label>
                    ' . $username . '
					<input ' . $hidden . ' type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->username . '" />
                    <br/>
					<label for="' . self::$messageContent . '">Your message :</label><br/>
					<textarea rows=6 cols=50 id="' . self::$messageContent . '" name="' . self::$messageContent . '">Type your message here..</textarea>
                    <br/>
                    <input type="submit" name="' . self::$submitMessage . '" value="Submit" />
                    
                    
				</fieldset>
            </form>
		';
    }

    private function generateMessageEditFormHTML(): string
    {
        $username = "<strong>$this->username</strong>";
        $id = $this->getMessageId();
        $msg = \Model\MessageStorage::getMessageById($id);
        $formHTML = $this->isAuthorizedEditor
            ? '<label for="' . self::$name . '">Username :</label>
        ' . $username . '
        <input hidden type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->username . '" />
        <input hidden type="text" id="' . self::$updateMessageId . '" name="' . self::$updateMessageId . '" value="' . $msg->id . '" />
        <br/>
        <label for="' . self::$messageContent . '">Your message :</label><br/>
        <textarea rows=6 cols=50" id="' . self::$messageContent . '" name="' . self::$messageContent . '">' . $msg->content . '</textarea>
        <br/>
        <input type="submit" name="' . self::$updateMessage . '" value="Update" />'
            : '';
        return '
        <a href=".">Account</a><br /><br />
            <form method="post" action=""?messages"">
				<fieldset>
					<legend>Edit message</legend>
                    <p id="' . self::$messageId . '">' . $this->message . '</p>'
            . $formHTML . '
				</fieldset>
            </form>
		';
    }
}
