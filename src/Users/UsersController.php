<?php

namespace Anax\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware {

    use \Anax\DI\TInjectable;
    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize() {
        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);
        date_default_timezone_set('Europe/Prague');
    }

    /**
     * List all users.
     *
     * @return void
     */
    public function listAction() {
        $all = $this->users->findAll();
        $this->theme->setTitle("List all users");
        $this->views->add('users/list-users', [
            'users' => $all,
            'title' => "View all users",
        ]);
        // $status = $this->di->statusMessage;
        // $this->views->addString($status->messagesHtml(), 'flash');
    }

    /**
     * List user with id.
     *
     * @param int $id of user to display
     *
     * @return void
     */
    public function idAction($id = null) {
        $user = $this->users->find($id);
        $questions = $user->getQuestions($user->acronym);
        $this->theme->setTitle($user->acronym);
        $answers = $user->getAnswers($user->acronym);
        $answerdQuestions = $user->linkAnswerToQuestion($user->acronym);
        $loggedOn = $user->isAuthenticated($user);

        $this->views->add('users/view', [
            'user' => $user,
            'loggedOn' => $loggedOn,
            'questions' => $questions,
            'answers' => $answers,
            'answerdQuestions' => $answerdQuestions,
        ]);
    }

    /**
     * Reset and setup database tabel with default users.
     *
     * @return void
     */
    public function setupAction() {

        $this->theme->setTitle("Reset and setup database with default users.");

        $table = [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'acronym' => ['varchar(20)', 'unique', 'not null'],
                'email' => ['varchar(80)'],
                'name' => ['varchar(80)'],
                'password' => ['varchar(255)'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
                'active' => ['datetime'],
                'timesLoggedOn' => ['integer'],
        ];

        $res = $this->users->setupTable($table);

        // Add some users
        $now = date('Y-m-d H:i:s');

        $this->users->create([
            'acronym' => 'Zelda',
            'email' => 'zelda@triforce.se',
            'name' => 'Princess Zelda',
            'password' => password_hash('zelda', PASSWORD_DEFAULT),
            'created' => $now,
            'active' => $now,
            'timesLoggedOn' => 1,
        ]);

        $this->users->create([
            'acronym' => 'Link',
            'email' => 'link@triforce.se',
            'name' => 'Link, Hero of time ',
            'password' => password_hash('link', PASSWORD_DEFAULT),
            'created' => $now,
            'active' => $now,
            'timesLoggedOn' => 1,
        ]);

        // $status = $this->di->statusMessage;
        // $status->addSuccessMessage("Databasen är återställd.");

        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

    /**
     * Add new user.
     *
     * @param string $acronym of user to add.
     *
     * @return void
     */
    public function addAction($acronym = null) {

        if (!isset($acronym)) {
          $form = new \Mos\HTMLForm\CForm([], [
                'acronym' => [
                  'type'        => 'text',
                  'label'       => 'Acronym: ',
                  'required'    => true,
                  'validation'  => ['not_empty'],
                ],
                'email' => [
                  'type'  => 'text',
                  'label' => 'E-post',
                  'required'    => true,
                  'validation'  => ['not_empty', 'email_adress',]

                  ],
                  'name' => [
                    'type'  => 'text',
                    'label' => 'Name',
                    'required'    => true,
                    'validation'  => ['not_empty',]

                  ],
                  'password' => [
                    'type'  => 'password',
                    'label' => 'Välj lösenord',
                    'required'    => true,
                    'validation'  => ['not_empty',]
                  ],
                'submit' => [
                    'type'      => 'submit',
                    'callback'  => function($form) {
                        // $form->AddOutput("<p><i>Form was submitted.</i></p>");
                        // $form->AddOutput("<p><b>Acronym: " . $form->Value('acronym') . "</b></p>");
                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);


            $this->views->add('users/view-default', [
                'title' => "Add a user",
                'main' => $form->getHTML(),
            ]);
            $this->theme->setVariable('title', "Add a user");

            // Check the status of the form
            $status = $form->check();

            if ($status === true) {
              $now = date('Y-m-d H:i:s');

              $this->users->create([
                'acronym' => $form->value('acronym'),
                'email' => $form->value('email'),
                'name' => $form->value('name'),
                'password' => password_hash($form->value('password'), PASSWORD_DEFAULT),
                'created' => $now,
                'active' => $now,
                'timesLoggedOn' => 1
                ]);

              // $url = $this->url->create('users/id/' . $this->users->id);
              $url = $this->url->create('users');
              $this->response->redirect($url);

              } else if ($status === false) {
                header("Location: " . $di->request->getCurrentUrl());
              }

        };
    }

    /**
     * Update user.
     *
     * @param integer $id of user to update.
     *
     * @return void
     */
    public function updateAction($id = null) {

        if (!isset($id)) {
            die("Missing id");
        }
        $this->theme->setTitle("Redigera profil");
        $user = $this->users->find($id);

        $form = $this->form->create([], [
            'acronym' => [
              'type'        => 'text',
              'label'       => 'Acronym: ',
              'required'    => true,
              'validation'  => ['not_empty'],
              'value'        => $user->acronym,
            ],
            'name' => [
              'type'        => 'text',
              'label'       => 'Name: ',
              'required'    => true,
              'validation'  => ['not_empty'],
              'value'        => $user->name,
            ],
            'email' => [
              'type'        => 'text',
              'label'       => 'Email: ',
              'required'    => true,
              'validation'  => ['not_empty', 'email_adress'],
              'value'        => $user->email,
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {


            // Collect data and unset the session variable
            $updated_user['id'] = $id;
            $updated_user['acronym'] = $_SESSION['form-save']['acronym']['value'];
            $updated_user['name'] = $_SESSION['form-save']['name']['value'];
            $updated_user['email'] = $_SESSION['form-save']['email']['value'];
            session_unset($_SESSION['form-save']);

            // Save updated user data
            $res = $this->users->save($updated_user);
            if($res) {
                $url = $this->url->create('users/list');
                $this->response->redirect($url);
            }

        } else if ($status === false) {
          echo "fail";

        }

        // Prepare the page content
        $this->views->add('users/view-default', [
            'title' => "Update user",
            'id' => $id,
            'main' => $form->getHTML(),
        ]);
    }


    /**
     * Delete user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function deleteAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        $res = $this->users->delete($id);

        $status = $this->di->statusMessage;
        $status->addWarningMessage("Användaren borttagen permanent.");

        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

    /**
     * Delete (soft) user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function softDeleteAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = date(DATE_RFC2822);

        $user = $this->users->find($id);

        $user->deleted = $now;
        $user->save();

        $status = $this->di->statusMessage;
        $status->addSuccessMessage("Användaren tillagd i papperskorgen.");

        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

    /**
     * Undelete (soft) user.
     *
     * @param integer $id of user to undelete.
     *
     * @return void
     */
    public function softUndeleteAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = date(DATE_RFC2822);

        $user = $this->users->find($id);

        $user->deleted = null;
        $user->save();

        $status = $this->di->statusMessage;
        $status->addSuccessMessage("Användaren hämtad från papperskorgen.");

        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

    /**
     * Make user active.
     *
     * @param integer $id of user to activate.
     *
     * @return void
     */
    public function activateAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = date(DATE_RFC2822);

        $user = $this->users->find($id);

        $user->active = $now;
        $user->save();

        $status = $this->di->statusMessage;
        $status->addSuccessMessage("Användare aktiverad.");

        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

    /**
     * Make user inactive.
     *
     * @param integer $id of user to deactivate.
     *
     * @return void
     */
    public function deactivateAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = date(DATE_RFC2822);

        $user = $this->users->find($id);

        $user->active = null;
        $user->save();

        $status = $this->di->statusMessage;
        $status->addSuccessMessage("Användare inaktiverad.");

        $url = $this->url->create('users/list');
        $this->response->redirect($url);

    }

    /**
     * List all active and not deleted users.
     *
     * @return void
     */
    public function activeAction() {
        $all = $this->users->query()
            ->where('active IS NOT NULL')
            ->andWhere('deleted is NULL')
            ->execute();

        $this->theme->setTitle("Users that are active");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are active",
        ]);
    }

    /**
     * List all soft-deleted users.
     *
     * @return void
     */
    public function inactiveAction() {
        $all = $this->users->query()
            ->where('active is NULL')
            ->execute();

        $this->theme->setTitle("Users that are inactive");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are inactive",
        ]);
    }

        /**
     * List all soft-deleted users.
     *
     * @return void
     */
    public function trashAction() {
        $all = $this->users->query()
            ->where('deleted is NOT NULL')
            ->execute();

        $this->theme->setTitle("Users that are soft-deleted");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are soft-deleted",
        ]);
    }
    private function getLoginForm() {
      $di = $this;
      $form = $this->form->create([], [
        'usernameoremail' => [
          'type'        => 'text',
          'label'       => 'Användarnamn eller e-post',
          'required'    => true,
          'maxlength'   => 255,
          'validation'  => array(
            'not_empty'
          )
        ],
        'password' => [
          'label'       => 'Lösenord',
          'type'        => 'password',
          'required'    => true,
          'validation'  => array(
            'not_empty'
          ),
        ],
        'submit' => [
          'value' 		=> 'Logga in',
          'type'      => 'submit',
          'callback'  => function ($form) use ($di) {
            if( $this->login($form->Value('usernameoremail'), $form->Value('password'))) {
              return true;
            }
            $form->AddOutput('Felaktigt användarnamn eller lösenord');
            return false;
            }
        ]
      ]);
      return $form;
    }

    public function loginAction() {
      $this->theme->setTitle("Logga in");
      $form = $this->getLoginForm();
      $status = $form->check();
      $formOrMessage = $form->getHTML(array('novalidate' => true));
      if ($status === true) {
        header("Location: " . $this->url->create('users'));
      }else if( $status === false ){
        header('Location: ' . $this->url->create('users/login'));
      }
      $this->views->add('users/login',[
        'form' => $formOrMessage,
      ], 'main');
    }


    public function logoutAction() {
      $this->theme->setTitle("Logga ut");
      $_SESSION['user'] = null;
      $this->views->add('users/logout', [
        'content' => "",
      ]);
    }
    public function login($acronym, $password) {
      $users = new \Anax\Users\User();
      $users->setDI($this->di);
      $id = $users->findIdByAcronym($acronym);
      $allUsers = $users->findAll();
      $id = $id - 1;
      $realPassword = $allUsers[$id]->password;
      $users->incrementTimesLoggedOn($allUsers[$id]->acronym);

      if(password_verify($password, $realPassword)){
        $_SESSION['user'] = $allUsers[$id];
        $sql = "UPDATE test_user SET active=? WHERE id=?;";
        $now = date('Y-m-d H:i:s');
        $params = array($now, $id + 1);
        $this->db->execute($sql, $params);
        return true;
      }else{
        return false;
      }
    }
    public function firstPageAction() {
      $mostLoggedOn = $this->users->getMostLoggedOn();
      $this->views->add('index/mostLoggedOn', [
        'mostLoggedOn' => $mostLoggedOn,
        ]);
    }

}
