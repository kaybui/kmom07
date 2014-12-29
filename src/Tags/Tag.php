<?php

namespace Anax\Tags;

/**
* Model for Questions.
*
*/
class Tag extends \Anax\MVC\CDatabaseModel {
  public function findAllTags() {
    $this->db->select()
    ->from($this->getSource())
    ->where("tags = ?");

    $this->db->execute([]);
    // dump($this);
    // return $this->db->fetchInto($this);
    $this->db->fetchInto($this);
    return $this->id;
  }
}
