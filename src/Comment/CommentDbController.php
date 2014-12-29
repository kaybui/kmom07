<?php

namespace Anax\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentDbController implements \Anax\DI\IInjectionAware {

    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize() {
        $this->comments = new \Anax\Comment\Comment();
        $this->comments->setDI($this->di);
    }

    /**
     * View all comments.
     *
     * @return void
     */
    public function viewAction() {
        $all = $this->comments->findAll();

        $this->views->add('comment/commentsdb', [
            'comments' => $all,
        ]);
    }

    /**
     * Reset and setup database tabel.
     *
     * @return void
     */
    public function setupAction() {

        $this->theme->setTitle("Reset and setup database.");

        $table = [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'comment' => ['tinytext'],
                'name' => ['varchar(80)'],
                'web' => ['varchar(80)'],
                'mail' => ['varchar(80)'],
                'timestamp' => ['datetime'],
                'ip' => ['varchar(20)'],
        ];

        $res = $this->comments->setupTable($table);

        // Add a comment
        // $this->comments->create([
        //     'comment' => '',
        //     'name' => 'No-name',
        //     'web' => 'www.default.se',
        //     'mail' => 'default@default.se',
        //     'timestamp' => time(),
        //     'ip' => $this->request->getServer('REMOTE_ADDR'),
        // ]);

        $url = $this->url->create('');
        $this->response->redirect($url);
    }

    /**
     * Add a comment.
     *
     * @return void
     */
    public function addAction($id = null) {

        if (isset($id)) {
            $edit_comment = $this->comments->find($id);
        } else {
            $edit_comment = (object) [
                'comment' => '',
                'name' => '',
                'web' => '',
                'mail' => '',
            ];
        }

        $form = $this->form->create([], [
            'comment' => [
              'type'        => 'text',
              'label'       => 'Comment: ',
              'required'    => true,
              'validation'  => ['not_empty'],
              'value'       => $edit_comment->comment,
            ],
            'name' => [
              'type'        => 'text',
              'label'       => 'Name: ',
              'required'    => true,
              'validation'  => ['not_empty'],
              'value'       => $edit_comment->name,
            ],
            'web' => [
              'type'        => 'text',
              'label'       => 'Web: ',
              'required'    => false,
              'validation'  => ['not_empty'],
              'value'       => $edit_comment->web,
            ],
            'mail' => [
              'type'        => 'text',
              'label'       => 'Email: ',
              'required'    => true,
              'validation'  => ['not_empty', 'email_adress'],
              'value'       => $edit_comment->mail,
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => function($form) {
                    $form->AddOutput("<p><i>Form was submitted.</i></p>");
                    $form->AddOutput("<p><b>Acronym: " . $form->Value('acronym') . "</b></p>");
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {

            // What to do if the form was submitted?
            $form->AddOutput("<p><i>Form submitted.</i></p>");

            // Get data from and and unset the session variable
            $comment['comment']     = $_SESSION['form-save']['comment']['value'];
            $comment['name']        = $_SESSION['form-save']['name']['value'];
            $comment['web']         = $_SESSION['form-save']['web']['value'];
            $comment['mail']        = $_SESSION['form-save']['mail']['value'];
            $comment['timestamp']   = time();
            $comment['ip']          = $this->request->getServer('REMOTE_ADDR');

            session_unset($_SESSION['form-save']);

            $this->comments->save($comment);

            // Route to prefered controller function
            $url = $this->url->create('comment/view');
            $this->response->redirect($url);

        } else if ($status === false) {

            // What to do when form could not be processed?
            $form->AddOutput("<p><i>Form submitted but did not checkout.</i></p>");
        }

        // Prepare the page content
        $this->views->add('comment/view-default', [
            'title' => "Lägg till kommentar",
            'main' => $form->getHTML(),
        ]);
        $this->theme->setVariable('title', "Lägg till kommentar");
    }

    public function removeIdAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        $res = $this->comments->delete($id);

        $url = $this->url->create('comment/view');
        $this->response->redirect($url);

    }

}
