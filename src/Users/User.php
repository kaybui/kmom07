<?php

namespace Anax\Users;

/**
 * Model for Users.
 *
 */
class User extends \Anax\MVC\CDatabaseModel {

  public function findIdByAcronym($acronym) {
    $this->db->select()
    ->from($this->getSource())
    ->where("acronym = ?");

    $this->db->execute([$acronym]);
    // dump($this);
    // return $this->db->fetchInto($this);
    $this->db->fetchInto($this);
    return $this->id;
  }
  public function getQuestions($acronym) {
    $sql = "SELECT * FROM test_user
              JOIN test_question
                  ON test_question.name=test_user.acronym";
    $questions =  $this->db->executeFetchAll($sql);
    $res = array();
    foreach($questions as $question) {
      if ($question->acronym == $acronym) {
        array_push($res, $question);
      }
    }
    return $res;
  }
  public function getAnswers($acronym) {
    $sql = "SELECT * FROM test_user
    JOIN test_answer
    ON test_answer.user=test_user.acronym";
    $answers =  $this->db->executeFetchAll($sql);
    $res = array();
    foreach($answers as $answer) {
      if ($answer->acronym == $acronym) {
        array_push($res, $answer);
      }
    }
    return $res;
  }
  public function linkAnswerToQuestion($acronym) {
    $sql = "SELECT * FROM test_question
              JOIN test_answer
                ON test_answer.questionID=test_question.id";
    $answers =  $this->db->executeFetchAll($sql);
    $res = array();
    foreach($answers as $answer) {
      if ($answer->user == $acronym) {
        array_push($res, $answer);
      }
    }
    return $res;
  }
  public function incrementTimesLoggedOn($acronym) {
    $sql = "SELECT timesLoggedOn FROM test_user WHERE acronym = ?";
    $params = array($acronym);
    $timesLoggedOn = $this->db->executeFetchAll($sql, $params);
    $val = $timesLoggedOn[0]->timesLoggedOn;
    $val++;
    $update = "UPDATE test_user SET timesLoggedOn=? WHERE acronym=?;";
    $params2 = array($val, $acronym);
    $this->db->execute($update, $params2);
  }
  public function getMostLoggedOn() {
    $sql = "SELECT * FROM test_user
    ORDER BY timesLoggedOn DESC LIMIT 3;";
    $mostLoggedOn =  $this->db->executeFetchAll($sql);
    return $mostLoggedOn;
  }
  public function isAuthenticated($user) {
    if (isset($_SESSION['user'])) {
      if($_SESSION['user']->acronym == $user->acronym){
        return true;
      }else{
        return false;
      }
    }

  }
}
