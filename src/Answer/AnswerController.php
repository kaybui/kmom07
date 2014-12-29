<?php

namespace Anax\Answer;

/**
* To attach question-flow to a page or some content.
*
*/
class AnswerController implements \Anax\DI\IInjectionAware {

  use \Anax\DI\TInjectable;
  protected $answers;
  /**
  * Initialize the controller.
  *
  * @return void
  */
  public function initialize() {
    $this->answer = new \Anax\Answer\Answer();
    $this->answer->setDI($this->di);
    $answers = array();
  }

}
