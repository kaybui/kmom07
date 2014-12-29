<?php

namespace Anax\Question;

/**
 * Model for Questions.
 *
 */
class Question extends \Anax\MVC\CDatabaseModel {

  public function answerQuestion($user, $answer, $questionID) {
    $sql = "INSERT INTO test_answer VALUES(?, ?, ?, ?, ?, ?);";
    $params = array('', $user, $answer, $questionID, 0, 0);
    $this->db->execute($sql, $params);
  }
  public function incrementNbrOfAnswers($questionID) {
    $sql = "SELECT nbrOfAnswers FROM test_question WHERE id=?;";
    $params = array($questionID);
    $res = $this->db->executeFetchAll($sql, $params);
    if ($res[0]->nbrOfAnswers == null) {
      $update = "UPDATE test_question SET nbrOfAnswers=? WHERE id=?;";
      $params = array(1, $questionID);
      $this->db->execute($update, $params);
    } else {
      $value = $res[0]->nbrOfAnswers;
      $value++;
      $update = "UPDATE test_question SET nbrOfAnswers=? WHERE id=?;";
      $params = array($value, $questionID);
      $this->db->execute($update, $params);
    }
  }

  public function getAnswers($id) {
    $sql = "SELECT *
      FROM test_question
        JOIN test_answer
          ON test_question.id=test_answer.questionID;";
    $answers = $this->db->executeFetchAll($sql);
    $res = array();
    foreach ($answers as $answer) {
      if ($answer->questionID == $id) {
        array_push($res, $answer);
      }
    }
    return $res;
  }
  public function getAnswer($id) {
    // $sql = "SELECT * FROM test_answer WHERE questionID = '$id';";
    $sql = "SELECT * FROM test_answer WHERE id = ?;";
    $params = array($id);
    $answer =  $this->db->executeFetchAll($sql, $params);
    return $answer;
  }
  public function getAnswerFromId($id) {
    $sql = "SELECT * FROM test_answer WHERE id = '$id';";
    $answer =  $this->db->executeFetchAll($sql);
    return $answer;
  }
  public function getContributor() {
    $sql = "SELECT * FROM test_answer
        JOIN test_user
          ON test_answer.user=test_user.acronym;";
    $users =  $this->db->executeFetchAll($sql);
    return $users;
  }
  public function getCommentators() {
    $sql = "SELECT * FROM test_comments
    JOIN test_user
    ON test_comments.user=test_user.acronym;";
    $users =  $this->db->executeFetchAll($sql);
    return $users;
  }
  public function getMostUsedTags() {
    $sql = "SELECT tag FROM test_tags
    ORDER BY timesUsed DESC LIMIT 5;";
    $mostUsed =  $this->db->executeFetchAll($sql);
    return $mostUsed;
  }
  public function getAllTags() {
    $sql = "SELECT tag FROM test_tags;";
    $allTags =  $this->db->executeFetchAll($sql);
    return $allTags;
  }
  public function saveTag($tag) {
    $sql ="SELECT * FROM test_tags";
    $all = $this->db->executeFetchAll($sql);
    if (empty($all)) {
      $sql = "INSERT INTO test_tags VALUES(?, ?);";
      $params = array($tag, 1);
      $this->db->execute($sql, $params);
      return;
    }
    $sql = "SELECT tag FROM test_tags;";
    $allTags = $this->db->executeFetchAll($sql);
    $array = array();
    foreach($allTags as $thisTag) {
      array_push($array, $thisTag->tag);
    }
    if (in_array($tag, $array)) {
      $sql = "SELECT * FROM test_tags WHERE tag = ?";
      $params = array($tag);
      $res = $this->db->executeFetchAll($sql, $params);
      $timesUsed = $res[0]->timesUsed;
      $timesUsed++;
      $update = "UPDATE test_tags SET timesUsed=? WHERE tag=?;";
      $updateParams = array($timesUsed, $tag);
      $this->db->execute($update, $updateParams);
    } else {
      $sql = "INSERT INTO test_tags VALUES(?, ?)";
      $params = array($tag, 1);
      $this->db->execute($sql, $params);
    }
  }
  public function getComments($id) {
    $sql = "SELECT *
    FROM test_question
      JOIN test_comments
        ON test_question.id=test_comments.id;";
    $comments = $this->db->executeFetchAll($sql);
    $res = array();
    if(!empty($comments)) {
      foreach($comments as $comment) {
        if ($comment->id == $id) {
          array_push($res, $comment);
        }

      }
    }
    return $res;
  }
  public function comment($id, $user, $comment, $posted) {
    $sql = "INSERT INTO test_comments VALUES(?, ?, ?, ?, ?, ?);";
    $params = array($id, $user, $comment, $posted, 0, '');
    $this->db->execute($sql, $params);
  }
  public function commentOnAnswer($id, $user, $comment, $posted) {
    $sql = "INSERT INTO test_commentsOnAnswers VALUES(?, ?, ?, ?, ?, ?);";
    $params = array($id, $user, $comment, $posted, '', 0);
    $this->db->execute($sql, $params);
  }
  public function getCommentsOnAnswers($id) {
    $sql = "SELECT *
    FROM test_answer
    JOIN test_commentsOnAnswers
    ON test_answer.id=test_commentsOnAnswers.id;";
    $comments = $this->db->executeFetchAll($sql);
    $res = array();
    if(!empty($comments)) {
      foreach($comments as $comment) {
        array_push($res, $comment);
      }
    }
    return $res;
  }
  public function cleanDB() {
    $sql = "DELETE FROM test_tags;
            DELETE FROM test_question;
            DELETE FROM test_answer;
            DELETE FROM test_comments;
            DELETE FROM test_commentsOnAnswers;
            UPDATE test_user SET timesLoggedOn=1;
    ";
    $this->db->execute($sql);
  }
  public function acceptAnswer($id, $acronym, $questionID) {
    if ($_SESSION['user']->acronym == $acronym) {
      $sql = "UPDATE test_answer SET accepted=? WHERE id=?;";
      $params = array(1, $id);
      $this->db->execute($sql, $params);
      $sql = "UPDATE test_question SET acceptedAnswer=? WHERE id=?";
      $params = array(1, $questionID);
      $this->db->execute($sql, $params);
    }

  }
  public function hasAcceptedAnswer($id) {
    $sql = "SELECT * FROM test_answer WHERE questionID = ? AND accepted = ?";
    $params = array($id, 1);
    $isAccepted = $this->db->executeFetchAll($sql, $params);
    if (empty($isAccepted)) {
      return;
    }
    $answerID = $isAccepted[0]->id;
    $res = array();
    if ($isAccepted[0]->accepted == 1) {
      array_push($res, true);
      array_push($res, $answerID);
    } else {
      array_push($res, false);
      array_push($res, $answerID);
    }
    return $res;
  }
  public function upVote($id = null) {
    $sql = "SELECT votes FROM test_question WHERE id=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes + 1;
    $sql = "UPDATE test_question SET votes=? WHERE id=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function downVote($id = null) {
    $sql = "SELECT votes FROM test_question WHERE id=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes - 1;
    $sql = "UPDATE test_question SET votes=? WHERE id=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function upVoteAnswer($id = null) {
    $sql = "SELECT votes FROM test_answer WHERE id=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes + 1;
    $sql = "UPDATE test_answer SET votes=? WHERE id=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function downVoteAnswer($id = null) {
    $sql = "SELECT votes FROM test_answer WHERE id=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes - 1;
    $sql = "UPDATE test_answer SET votes=? WHERE id=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function upVoteCommentOnQuestion($id = null) {
    $sql = "SELECT votes FROM test_comments WHERE commentID=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes + 1;
    $sql = "UPDATE test_comments SET votes=? WHERE commentID=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function downVoteCommentOnQuestion($id = null) {
    $sql = "SELECT votes FROM test_comments WHERE commentID=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes - 1;
    $sql = "UPDATE test_comments SET votes=? WHERE commentID=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function upVoteCommentOnAnswer($id = null) {
    $sql = "SELECT votes FROM test_commentsOnAnswers WHERE commentID=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes + 1;
    $sql = "UPDATE test_commentsOnAnswers SET votes=? WHERE commentID=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function downVoteCommentOnAnswer($id = null) {
    $sql = "SELECT votes FROM test_commentsOnAnswers WHERE commentID=?;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    $vote = $res[0]->votes - 1;
    $sql = "UPDATE test_commentsOnAnswers SET votes=? WHERE commentID=?;";
    $params = array($vote, $id);
    $this->db->execute($sql, $params);
  }
  public function sortByVotes() {
    $sql ="SELECT * FROM test_question ORDER BY votes ASC;";
    $res = $this->db->executeFetchAll($sql);
    return $res;
  }
  public function sortbyNbrOfAnswers() {
    $sql ="SELECT * FROM test_question ORDER BY nbrOfAnswers ASC;";
    $res = $this->db->executeFetchAll($sql);
    return $res;
  }
  public function sortByDate() {
    $sql ="SELECT * FROM test_question ORDER BY timestamp ASC;";
    $res = $this->db->executeFetchAll($sql);
    return $res;
  }
  public function firstPage() {
    $sql ="SELECT * FROM test_question ORDER BY timestamp DESC LIMIT 3;";
    $res = $this->db->executeFetchAll($sql);
    return $res;
  }
  public function sortAnswersByVotes() {
    $sql ="SELECT * FROM test_answer WHERE questionID = ? ORDER BY votes DESC;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    return $res;
  }
  public function sortAnswersByDate() {
    $sql ="SELECT * FROM test_answer WHERE questionID = ? ORDER BY id DESC;";
    $params = array($id);
    $res = $this->db->executeFetchAll($sql, $params);
    return $res;
  }


  public function setup() {
    $sql = "CREATE TABLE `test_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `acronym` varchar(20) NOT NULL,
    `email` varchar(80) DEFAULT NULL,
    `name` varchar(80) DEFAULT NULL,
    `password` varchar(255) DEFAULT NULL,
    `created` datetime DEFAULT NULL,
    `updated` datetime DEFAULT NULL,
    `deleted` datetime DEFAULT NULL,
    `active` datetime DEFAULT NULL,
    `timesLoggedOn` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `acronym` (`acronym`)
    ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
    ";
    $this->db->execute($sql);

    $sql = "CREATE TABLE `test_question` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `question` tinytext,
    `name` varchar(80) DEFAULT NULL,
    `mail` varchar(80) DEFAULT NULL,
    `timestamp` datetime DEFAULT NULL,
    `ip` varchar(20) DEFAULT NULL,
    `title` varchar(80) DEFAULT NULL,
    `tags` text,
    `votes` int(11) DEFAULT '0',
    `nbrOfAnswers` int(11) DEFAULT '0',
    `acceptedAnswer` int(11) DEFAULT '0',
    `userID` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
    ";
    $this->db->execute($sql);

    $sql = "CREATE TABLE `test_tags` (
    `tag` varchar(90) DEFAULT NULL,
    `timesUsed` int(11) DEFAULT NULL,
    UNIQUE KEY `tag_UNIQUE` (`tag`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ";
    $this->db->execute($sql);

    $sql = "CREATE TABLE `test_commentsOnAnswers` (
    `id` int(11) DEFAULT NULL,
    `user` varchar(45) DEFAULT NULL,
    `comment` tinytext,
    `posted` timestamp NULL DEFAULT NULL,
    `commentID` int(11) NOT NULL AUTO_INCREMENT,
    `votes` int(11) DEFAULT '0',
    PRIMARY KEY (`commentID`)
    ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
    ";
    $this->db->execute($sql);

    $sql = "CREATE TABLE `test_comments` (
    `id` int(11) DEFAULT NULL,
    `user` varchar(45) DEFAULT NULL,
    `comment` tinytext,
    `posted` timestamp NULL DEFAULT NULL,
    `votes` int(11) DEFAULT '0',
    `commentID` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`commentID`)
    ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
    ";
    $this->db->execute($sql);

    $sql = "CREATE TABLE `test_answer` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user` varchar(45) DEFAULT NULL,
    `answer` tinytext,
    `questionID` int(11) DEFAULT NULL,
    `accepted` int(11) DEFAULT '0',
    `votes` int(11) DEFAULT '0',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=latin1;
    ";
    $this->db->execute($sql);

  }




}
