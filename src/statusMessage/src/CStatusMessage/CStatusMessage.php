<?php

namespace niw\CStatusMessage;

/**
* Class for display different type of status updates/messages in session.
* The messages are styled in four different styles for variuos message.
* You can display several massages at the same time.
*
*/
class CStatusMessage
{
  use \niw\DI\TInjectable;

  private $sessionVariable = 'statusMessage';
  private $messageTypes = ['info', 'error', 'success', 'warning'];

  // Variable for the anax session object
  private $session = null;

  // All messages will be stored in $allMessages when instace created
  private $allMessages = null;

  public function __construct($di) {

    $this->di = $di;
    $this->session = $this->di->session();

    if($this->session->has($this->sessionVariable))
    {
      $this->retrieveMessages();
    }
  }

  private function addMessage($type = 'info', $message) {
    $statusMessage = ['type' => $type, 'message' => $message];
    if (is_null($this->allMessages)) {
      $this->allMessages = array();
    }
    array_push($this->allMessages, $statusMessage);
    $this->session->set($this->sessionVariable, $this->allMessages);
  }

  public function addInfoMessage($message) {
    $this->addMessage('info', $message);
  }

  public function addErrorMessage($message) {
    $this->addMessage('error', $message);
  }

  public function addSuccessMessage($message) {
    $this->addMessage('success', $message);
  }

  public function addWarningMessage($message) {
    $this->addMessage('warning', $message);
  }
  public function isEmpty() {
    if (is_null($this->allMessages)) {
      return true;
    }
    return false;
  }

  public function clearMessages() {
    $this->session->set($this->sessionVariable, null);
  }

  public function retrieveMessages() {
    $this->allMessages = $this->session->get($this->sessionVariable);
  }

  public function messagesHtml() {
    $html = "";

    if(is_null($this->allMessages))
    return $html;

    foreach ($this->allMessages as $message) {
      $type = $message['type'];
      $message = $message['message'];

      $html .= "<div class='message-".$type."'>".$message."</div>";
    }

    $this->clearMessages();

    return $html;
  }
}
