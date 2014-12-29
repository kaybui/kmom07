<?php

namespace Anax\Question;

/**
* To attach question-flow to a page or some content.
*
*/
class QuestionController implements \Anax\DI\IInjectionAware {

  use \Anax\DI\TInjectable;

  /**
  * Initialize the controller.
  *
  * @return void
  */
  public function initialize() {
    $this->question = new \Anax\Question\Question();
    $this->question->setDI($this->di);
    date_default_timezone_set('Europe/Prague');
  }

  public function viewAction() {
    $this->theme->setTitle("Alla frågor");
    $all = $this->question->findAll();
    // $all = $this->question->getQuestions();
    $this->views->add('question/view', [
      'question' => $all,
    ]);
  }
  public function cleanAction() {
    $this->question->cleanDB();
    $url = $this->url->create('');
    $this->response->redirect($url);
  }

  /**
  * Reset and setup database tabel.
  *
  * @return void
  */
  public function setupAction() {

    $this->theme->setTitle("Reset and setup database.");
    $this->question->setup();
  
     $table = [
       'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
       'question' => ['tinytext'],
       'name' => ['varchar(80)'],
       'mail' => ['varchar(80)'],
      'timestamp' => ['datetime'],
       'ip' => ['varchar(20)'],
       'title' => ['varchar(80)'],
      'tags' => ['text'],
       'votes' => ['integer'],
       'nbrOfAnswers' => ['integer'],
       'acceptedAnswers' => ['integer'],
       'userID' => ['integer'],
     ];
    
     $res = $this->question->setupTable($table);
     $now = date('Y-m-d H:i:s');
    
 $url = $this->url->create('question');
     $this->response->redirect($url);
  }

  /**
  * Add a question.
  *
  * @return void
  */
  public function addAction($id = null) {
    if (!isset($_SESSION['user'])) {
      $this->theme->setTitle("Logga in");
      $this->views->add('users/notLoggedIn', [
        'content' => '<h1>Skapa profil eller logga in</h1>',
      ]);
      return;
    }
    if (isset($id)) {
      $edit_question = $this->question->find($id);

    } else {
      $edit_question = (object) [
        'question' => '',
        'name' => '',
        'mail' => '',
        'title' => '',
      ];
    }
    $now = date('Y-m-d H:i:s');
    $form = $this->form->create([], [
      'title' => [
        'type'        => 'text',
        'label'       => 'Titel: ',
        'required'    => true,
        'validation'  => ['not_empty'],
        'value'       => $edit_question->mail,
      ],
      'question' => [
        'type'        => 'textarea',
        'label'       => 'Fråga: ',
        'required'    => true,
        'validation'  => ['not_empty'],
        'value'       => $edit_question->question,
      ],
      'name' => [
        'type'        => 'hidden',
        'label'       => 'Användare: ',
        'required'    => true,
        'validation'  => ['not_empty'],
        'value'       => $_SESSION['user']->acronym,
      ],
      'mail' => [
        'type'        => 'hidden',
        'label'       => 'Email: ',
        'required'    => true,
        'validation'  => ['not_empty', 'email_adress'],
        'value'       => $_SESSION['user']->email,
      ],
      'tags' => [
        'type'        => 'textarea',
        'label'       => 'Taggar: Separera med ny rad ',
        'required'    => true,
        'validation'  => ['not_empty',],
        'value'       => '',
      ],
      'userID' => [
        'type'        => 'hidden',
        'label'       => 'AnvändarID',
        'required'    => true,
        'validation'  => ['not_empty',],
        'value'       => $_SESSION['user']->id,
      ],
      'submit' => [
        'type'      => 'submit',
        'callback'  => function($form) {
          $form->saveInSession = true;
          return true;
        }
        ],
        ]);

        $status = $form->check();

        if ($status === true) {
          $question['question']     = $_SESSION['form-save']['question']['value'];
          $question['question'] = $this->textFilter->doFilter($question['question'], 'shortcode, markdown');

          $question['name']        = $_SESSION['form-save']['name']['value'];
          $question['mail']        = $_SESSION['form-save']['mail']['value'];
          $question['tags']        = $_SESSION['form-save']['tags']['value'];
          $question['timestamp']   = $now;
          $question['ip']          = $this->request->getServer('REMOTE_ADDR');
          $question['title']       = $_SESSION['form-save']['title']['value'];
          $question['userID']       = $_SESSION['form-save']['userID']['value'];
          // $question['nbrOfAnswers']       = $_SESSION['form-save']['nbrOfAnswers']['value'];

          $tags = explode("\n", str_replace(' ', '', $question['tags']));

          foreach($tags as $tag) {
            $tag = trim(preg_replace('/\s+/', ' ', $tag));
            $this->question->saveTag($tag);
          }

          // $this->question->saveTag('niwhede');


          // session_unset($_SESSION['form-save']);
          unset($_SESSION['form-save']);

          $this->question->save($question);

          // Route to prefered controller function
          $url = $this->url->create('question/view');
          $this->response->redirect($url);

        } else if ($status === false) {

          // What to do when form could not be processed?
          $form->AddOutput("<p><i>Något gick fel.</i></p>");
        }

        // Prepare the page content
        $this->views->add('question/view-default', [
        'title' => "Ställ en fråga",
        'main' => $form->getHTML(),
        ]);
        $this->theme->setVariable('title', "Ställ en fråga");
      }

      public function removeIdAction($id = null) {
        if (!isset($id)) {
          die("Missing id");
        }

        $res = $this->question->delete($id);

        $url = $this->url->create('question/view');
        $this->response->redirect($url);

      }
      public function idAction($id = null) {
        $question = $this->question->find($id);
        $this->theme->setTitle("Läs fråga");
        $answers = $question->getAnswers($id);
        $contributors = $question->getContributor();
        $commentators = $question->getCommentators();
        $comments = $question->getComments($id);
        $commentsOnAnswers = $question->getCommentsOnAnswers($id);

        if (!empty($answers)) {
          $hasAcceptedAnswer = $question->hasAcceptedAnswer($id);
        } else {
          $hasAcceptedAnswer = array();
        }


        $this->views->add('question/question', [
          'id'    => $id,
          'question' => $question,
          'answers' => $answers,
          'contributors' => $contributors,
          'comments' => $comments,
          'commentators' => $commentators,
          'commentsOnAnswers' => $commentsOnAnswers,
          'hasAcceptedAnswer' => $hasAcceptedAnswer,
        ]);

      }
      public function allTagsAction() {
        $this->theme->setTitle("Alla Taggar");
        $list = array();
        $allTags = $this->question->getAllTags();
        foreach($allTags as $tag) {
          array_push($list, $tag->tag);
        }

        $this->views->add('tags/view-allTags', [
          'tags' => $list,
        ]);
      }

    public function tagIdAction($id = null) {
      $questions = $this->question->findAll();
      $list = array();
      foreach($questions as $question) {
        $tags = explode("\n", str_replace(' ', '', $question->tags));
        foreach($tags as $tag) {
          $tag = trim(preg_replace('/\s+/', ' ', $tag));
          if ($tag == $id) {
            array_push($list, $question);
          }
        }
      }
      $this->views->add('question/view', [
        'question' => $list,
      ]);
    }
    public function answerAction($id = null) {
      $thisQuestion = $this->question->find($id);
      $answer = "";
      $this->theme->setTitle("Besvara frågan");
      $form = $this->form->create([], [
        'title' => [
          'type'        => 'hidden',
          'label'       => 'Titel: ',
          'required'    => false,
          // 'validation'  => ['not_empty'],
          'value'       => $thisQuestion->title,
        ],
        'answer' => [
          'type'        => 'textarea',
          'label'       => 'Titel: ',
          'required'    => true,
          // 'validation'  => [],
          'value'       => "",
        ],
        'question' => [
          'type'        => 'hidden',
          'label'       => 'Fråga: ',
          'required'    => false,
          // 'validation'  => ['not_empty'],
          'value'       => $thisQuestion->question,
        ],
        'name' => [
          'type'        => 'hidden',
          'label'       => 'Användare: ',
          'required'    => false,
          // 'validation'  => ['not_empty'],
          'value'       => $thisQuestion->name,
        ],
        'mail' => [
          'type'        => 'hidden',
          'label'       => 'Email: ',
          'required'    => false,
          // 'validation'  => ['not_empty',],
          'value'       => $thisQuestion->mail,
        ],
        'tags' => [
          'type'        => 'hidden',
          'label'       => 'Taggar: ',
          'required'    => false,
          // 'validation'  => ['not_empty',],
          'value'       => $thisQuestion->tags,
        ],
        'submit' => [
          'type'      => 'submit',
          'callback'  => function($form) {
            $form->saveInSession = true;
            return true;
          }
        ],
        ]);

        $status = $form->check();

        if ($status === true) {
          $question['question']     = $_SESSION['form-save']['question']['value'];
          $question['question']     = $this->textFilter->doFilter($question['question'], 'shortcode, markdown');
          $question['title']        = $_SESSION['form-save']['title']['value'];
          $answer       = $_SESSION['form-save']['answer']['value'];
          $answer = $this->textFilter->doFilter($answer, 'shortcode, markdown');
          $user = $_SESSION['user']->acronym;
          $this->question->answerQuestion($user, $answer, $id, 0);


          // session_unset($_SESSION['form-save']);
          unset($_SESSION['form-save']);

          $this->question->save($question);
          $url = $this->url->create('question/id/' . $id);
          $this->response->redirect($url);

        } else if ($status === false) {
          echo "fail";
        }
        $question = $this->question->find($id);
        $answers = $question->getAnswers($id);
        $contributors = $question->getContributor();
        $question->incrementNbrOfAnswers($id);

      $this->views->add('question/question', [
        'question' => $thisQuestion,
        'form' => $form->getHTML(),
        'contributors' => $contributors,
        'answers' => $answers,
        ]);
    }
    public function firstPageAction() {
      $content = '';
      $res = $this->question->firstPage();
      $mostUsed = $this->question->getMostUsedTags();
      $this->views->add('index/start', [
        'content' => $content,
        'questions' => $res,
        'mostUsedTags' => $mostUsed,
      ]);
    }
    public function commentAction($id = null) {
      $form = $this->form->create([], [
        'comment' => [
          'type'        => 'text',
          'label'       => 'Kommentar: ',
          'required'    => false,
          // 'validation'  => ['not_empty'],
          'value'       => '',
        ],
        'submit' => [
          'type'      => 'submit',
          'callback'  => function($form) {
            $form->saveInSession = true;
            return true;
          }
        ],
        ]);
        $status = $form->check();

        if ($status === true) {
          $question['comment']     = $_SESSION['form-save']['comment']['value'];
          $question['comment']     = $this->textFilter->doFilter($question['comment'], 'shortcode, markdown');
          $comment       = $question['comment'];
          $user = $_SESSION['user']->acronym;
          $now = date('Y-m-d H:i:s');
          $this->question->comment($id, $user, $comment, $now);


          // session_unset($_SESSION['form-save']);
          unset($_SESSION['form-save']);

          // $this->question->save($question);
          $url = $this->url->create('question/id/' . $id);
          $this->response->redirect($url);

        } else if ($status === false) {
          echo "fail";
        }

        $question = $this->question->find($id);
        $commentators = $question->getCommentators();
        $comments = $question->getComments($id);
        $commentsOnAnswers = $question->getCommentsOnAnswers($id);
        $this->views->add('question/comment', [
          'question' => $question,
          'commentators' => $commentators,
          'comments' => $comments,
          'commentsOnAnswers' => $commentsOnAnswers,
          'form' => $form->getHtml(),
          ]);
    }
    public function commentOnAnswerAction($id = null, $questionID = NULL) {
      $form = $this->form->create([], [
        'comment' => [
          'type'        => 'text',
          'label'       => 'Kommentar: ',
          'required'    => false,
          // 'validation'  => ['not_empty'],
          'value'       => '',
        ],
        'submit' => [
          'type'      => 'submit',
          'callback'  => function($form) {
            $form->saveInSession = true;
            return true;
          }
        ],
        ]);
        $status = $form->check();

        if ($status === true) {
          $question['comment']     = $_SESSION['form-save']['comment']['value'];
          $question['comment']     = $this->textFilter->doFilter($question['comment'], 'shortcode, markdown');
          $comment       = $question['comment'];
          $user = $_SESSION['user']->acronym;
          $now = date('Y-m-d H:i:s');
          $this->question->commentOnAnswer($id, $user, $comment, $now);

          // session_unset($_SESSION['form-save']);
          unset($_SESSION['form-save']);

          $url = $this->url->create('question/id/' . $questionID);
          $this->response->redirect($url);

        }
          // $question = $this->question->find($id);
          // $answers = $question->getAnswers($id);
          // $contributors = $question->getContributor();
          // $comments = $question->getCommentsOnAnswers($id);

          $this->views->add('question/comment', [
            // 'question' => $question,
            // 'commentators' => $commentators,
            // 'answers' => $answers,
            // 'contributors' => $contributors,
            // 'comments' => $comments,
            'form' => $form->getHtml(),
            ]);
    }
    public function acceptAction($id = null, $questionID = null, $acronym = null) {
      $answer = $this->question->getAnswerFromId($id);
      $this->question->acceptAnswer($answer[0]->id, $acronym, $questionID);
      $url = $this->url->create('question/id/' . $questionID);
      $this->response->redirect($url);
    }
    public function upVoteAction($id = null) {
      $question = $this->question->find($id);
      $question->upvote($id);
      $url = $this->url->create('question/id/' . $id);
      $this->response->redirect($url);
    }
    public function downVoteAction($id = null) {
      $question = $this->question->find($id);
      $question->downVote($id);
      $url = $this->url->create('question/id/' . $id);
      $this->response->redirect($url);
    }
    public function upVoteAnswerAction($questionID = null, $id = null) {
      $this->question->upVoteAnswer($id);
      $url = $this->url->create('question/id/' . $questionID);
      $this->response->redirect($url);
    }
    public function downVoteAnswerAction($questionID = null, $id = null) {
      $this->question->downVoteAnswer($id);
      $url = $this->url->create('question/id/' . $questionID);
      $this->response->redirect($url);
    }
    public function upVoteCommentOnQuestionAction($id = null, $questionID = null) {
      $this->question->upVoteCommentOnQuestion($id);
      $url = $this->url->create('question/id/' . $questionID);
      $this->response->redirect($url);
    }
    public function downVoteCommentOnQuestionAction($id = null, $questionID = null) {
      $this->question->downVoteCommentOnQuestion($id);
      $url = $this->url->create('question/id/' . $questionID);
      $this->response->redirect($url);
    }
    public function upVoteCommentOnAnswerAction($id = null, $questionID) {
      $this->question->upVoteCommentOnAnswer($id);
      $url = $this->url->create('question/id/' . $questionID);
      $this->response->redirect($url);
    }
    public function downVoteCommentOnAnswerAction($id = null, $questionID) {
      $this->question->downVoteCommentOnAnswer($id);
      $url = $this->url->create('question/id/' . $questionID);
      $this->response->redirect($url);
    }
    public function sortAction($option) {
      if ($option == "votes") {
        $all = $this->question->sortByVotes();
      } else if ($option == "nbrOfAnswers") {
        $all = $this->question->sortbyNbrOfAnswers();
      } else if ($option == "date") {
        $all = $this->question->sortByDate();
      }
      $this->views->add('question/view', [
        'question' => $all,
        ]);
    }
    public function sortAnswersAction($id = null, $option = null) {
      if ($option == "votes") {
        $all = $this->question->sortAnswersByVotes($id);
      } else if ($option == "date") {
        $all = $this->question->sortAnswersByDate($id);
      }
      $question = $this->question->find($id);
      $this->theme->setTitle("Läs fråga");
      $answers = $question->getAnswers($id);
      $contributors = $question->getContributor();
      $commentators = $question->getCommentators();
      $comments = $question->getComments($id);
      $commentsOnAnswers = $question->getCommentsOnAnswers($id);

      if (!empty($answers)) {
        $hasAcceptedAnswer = $question->hasAcceptedAnswer($id);
      } else {
        $hasAcceptedAnswer = array();
      }


      $this->views->add('question/question', [
        'id'    => $id,
        'question' => $question,
        'answers' => $all,
        'contributors' => $contributors,
        'comments' => $comments,
        'commentators' => $commentators,
        'commentsOnAnswers' => $commentsOnAnswers,
        'hasAcceptedAnswer' => $hasAcceptedAnswer,
        ]);

      }
}
