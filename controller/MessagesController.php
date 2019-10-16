<?php

namespace Controller;

class MessagesController extends Controller
{

    public function updateState(): void
    {
        try {
            $this->updateView();
        } catch (\Exception $e) {
            $this->view->setMessage($e->getMessage());
        }
    }

    private function updateView()
    {
        $messages = $this->getMessages();
        $this->view->setMessages($messages);
    }

    private function getMessages()
    {
        return ['Hello', 'Bye'];
    }
}
