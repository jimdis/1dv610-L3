<?php

namespace View;

class MessagesView extends View
{
    private static $submitMessage = __CLASS__ . '::submitMessage';
    private static $updateMessage = __CLASS__ . '::updateMessage';
    private static $updateMessageId = __CLASS__ . '::updateMessageId';
    private static $name = __CLASS__ . '::UserName';
    private static $messageContent = __CLASS__ . '::messageContent';
    private static $messageId = __CLASS__ . '::Message';
    private $username;
    private $message = '';

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

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    private function showEditMode(): bool
    {
        return isset($_GET["edit"]);
    }

    private function getMessageId(): int
    {
        return $_GET["edit"];
    }


    /**
     * Create HTTP response
     *
     * Should be called after a login attempt has been determined
     *
     * @return  void BUT writes to standard output and cookies!
     */
    public function response(): string
    {
        $response = $this->generateViewHTML();
        return $response;
    }

    private function generateViewHTML(): string
    {
        $form = $this->showEditMode() ? $this->generateMessageEditFormHTML($this->message) : $this->generateMessageFormHTML($this->message);
        $messageTable = \View\MessagesTable::generateMessagesTableHTML();
        $html = $form . '<br/><h2>Message Board</h2>' . $messageTable;
        return $html;
    }

    private function generateMessageFormHTML($message): string
    {
        $isLoggedIn = $this->storage->getIsAuthenticated();
        $linkText = $isLoggedIn ? 'Account' : 'Go to login';
        $username = $this->username != null ? "<strong>$this->username</strong>" : '';
        $hidden = $this->username != null ? 'hidden' : '';
        return '
        <a href=".">' . $linkText . '</a><br /><br />
            <form method="post" action=""?messages"">
				<fieldset>
					<legend>Write a new message</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
                    
                    <label for="' . self::$name . '">Username :</label>
                    ' . $username . '
					<input ' . $hidden . ' type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->username . '" />
                    <br/>
					<label for="' . self::$messageContent . '">Your message :</label><br/>
					<textarea rows=6 cols=50" id="' . self::$messageContent . '" name="' . self::$messageContent . '">Type your message here..</textarea>
                    <br/>
                    <input type="submit" name="' . self::$submitMessage . '" value="Submit" />
                    
                    
				</fieldset>
            </form>
		';
    }

    //TODO in all views: remove $message duplication
    private function generateMessageEditFormHTML($message): string
    {
        $isLoggedIn = $this->storage->getIsAuthenticated();
        if (!$isLoggedIn) {
            throw new \Exception('You must be logged in to view this page');
        }
        //TODO: Forsätt här - läs in message från storage baserat på query.
        $username = "<strong>$this->username</strong>";
        $id = $this->getMessageId();
        $msg = \Model\MessageStorage::getMessageById($id);
        return '
        <a href=".">Account</a><br /><br />
            <form method="post" action=""?messages"">
				<fieldset>
					<legend>Edit message</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
                    
                    <label for="' . self::$name . '">Username :</label>
                    ' . $username . '
                    <input hidden type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->username . '" />
                    <input hidden type="text" id="' . self::$updateMessageId . '" name="' . self::$updateMessageId . '" value="' . $msg->id . '" />
                    <br/>
					<label for="' . self::$messageContent . '">Your message :</label><br/>
					<textarea rows=6 cols=50" id="' . self::$messageContent . '" name="' . self::$messageContent . '">' . $msg->content . '</textarea>
                    <br/>
                    <input type="submit" name="' . self::$updateMessage . '" value="Update" />
                    
                    
				</fieldset>
            </form>
		';
    }
}
