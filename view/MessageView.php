<?php

namespace View;

class MessageView extends View
{
    private static $submitMessage = __CLASS__ . '::submitMessage';
    private static $updateMessage = __CLASS__ . '::updateMessage';
    private static $updateMessageId = __CLASS__ . '::updateMessageId';
    private static $name = __CLASS__ . '::UserName';
    private static $messageContent = __CLASS__ . '::messageContent';
    private static $feedbackMessageId = __CLASS__ . '::feedbackMessage';
    private $showEditForm = false;
    private $editMessageId;
    private $contentInput = '';
    private $isAuthorizedEditor = true;

    public function getNewMessage(): \Model\Message
    {
        $author = $_POST[self::$name] ?? '';
        $content = $_POST[self::$messageContent] ?? '';
        $id = $_POST[self::$updateMessageId] ?? null;
        $this->contentInput = $this->getFormMessage();
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

    public function getFormMessage(): string
    {
        return isset($_POST[self::$messageContent])
            ? \Model\SanitizeInput::sanitize($_POST[self::$messageContent])
            : '';
    }

    public function setShowEditForm(bool $bool)
    {
        $this->showEditForm = $bool;
    }

    public function setEditMessageId(int $id)
    {
        $this->editMessageId = $id;
    }

    public function getUpdatedMessageId(): int
    {
        return $_POST[self::$updateMessageId];
    }

    public function setIsAuthorizedEditor(bool $bool)
    {
        $this->isAuthorizedEditor = $bool;
    }

    public function setContentInput(string $string)
    {
        $this->contentInput = $string;
    }

    public function show(): string
    {
        $linkText = $this->storage->getUserIsAuthenticated()
            ? 'Account'
            : 'Go to login';
        $form = $this->showEditForm
            ? $this->generateMessageEditFormHTML($linkText)
            : $this->generateMessageFormHTML($linkText);
        $html = $form . '<br/><h2>Message Board</h2>' . \View\MessageTable::showAllMessages();
        return $html;
    }

    private function generateMessageFormHTML(string $linkText): string
    {
        $isLoggedIn = $this->storage->getUserIsAuthenticated();
        $username = $isLoggedIn ? '<strong>' . $this->storage->getUsername() . '</strong>' : '';
        $hidden = $isLoggedIn ? 'hidden' : '';
        return '
        <a href=".">' . $linkText . '</a><br /><br />
            <form method="post" action="?' . \Model\Routes::$messages . '">
				<fieldset>
					<legend>Write a new message</legend>
					<p id="' . self::$feedbackMessageId . '">' . $this->message . '</p>
                    
                    <label for="' . self::$name . '">Username :</label>
                    ' . $username . '
					<input ' . $hidden . ' type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getUsername() . '" />
                    <br/>
					<label for="' . self::$messageContent . '">Your message :</label><br/>
					<textarea rows=6 cols=50 id="' . self::$messageContent . '" name="' . self::$messageContent . '">' . $this->contentInput . '</textarea>
                    <br/>
                    <input type="submit" name="' . self::$submitMessage . '" value="Submit" />
                    
                    
				</fieldset>
            </form>
		';
    }

    private function generateMessageEditFormHTML(string $linkText): string
    {
        $msg = \Model\MessageDAL::getMessageById($this->editMessageId);
        $formHTML = $this->isAuthorizedEditor
            ? '<label for="' . self::$name . '">Username :</label>
        <strong>' . $this->storage->getUsername() . '</strong>
        <input hidden type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->storage->getUsername() . '" />
        <input hidden type="text" id="' . self::$updateMessageId . '" name="' . self::$updateMessageId . '" value="' . $msg->id . '" />
        <br/>
        <label for="' . self::$messageContent . '">Your message :</label><br/>
        <textarea rows=6 cols=50 id="' . self::$messageContent . '" name="' . self::$messageContent . '">' . $msg->content . '</textarea>
        <br/>
        <input type="submit" name="' . self::$updateMessage . '" value="Update" />'
            : '';
        return '
        <a href=".">' . $linkText . '</a><br /><br />
            <form method="post" action="?' . \Model\Routes::$messages . '">
				<fieldset>
					<legend>Edit message</legend>
                    <p id="' . self::$feedbackMessageId . '">' . $this->message . '</p>'
            . $formHTML . '
				</fieldset>
            </form>
		';
    }
    private function getUsername(): string
    {
        return isset($_POST[self::$name])
            ? \Model\SanitizeInput::sanitize($_POST[self::$name])
            : $this->storage->getUsername();
    }
}
