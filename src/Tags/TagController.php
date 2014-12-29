<?php

namespace Anax\Tags;

/**
* To attach question-flow to a page or some content.
*
*/
class TagController implements \Anax\DI\IInjectionAware {

  use \Anax\DI\TInjectable;

  /**
  * Initialize the controller.
  *
  * @return void
  */
  public function initialize() {
    $this->tag = new \Anax\Tags\Tag();
    $this->tag->setDI($this->di);
  }
  public function viewAction() {
    $this->db->select()
    ->from($this->getSource())
    ->where("acronym = ?");
  }

}
