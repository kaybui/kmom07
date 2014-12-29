<?php

require __DIR__.'/config_with_app.php';

// Create services and inject into the app.
    $di  = new \Anax\DI\CDIFactoryDefault();
    $di->set('form', '\Mos\HTMLForm\CForm');

    $di->setShared('db', function() {
        $db = new \Mos\Database\CDatabaseBasic();
        $db->setOptions(require ANAX_APP_PATH . 'config/config_mysql.php');
        $db->connect();
        return $db;
    });

    $di->set('UsersController', function() use ($di) {
        $controller = new \Anax\Users\UsersController();
        $controller->setDI($di);
        return $controller;
    });
    $di->set('CommentController', function() use ($di) {
        $controller = new \Anax\Comment\CommentDbController();
        $controller->setDI($di);
        return $controller;
    });
    $di->set('statusMessage', function() use ($di) {
      $flashMessage = new \Anax\flashingmessage\CStatusMessage($di);
      $flashMessage->setDI($di);
      return $flashMessage;
    });
    $di->set('QuestionController', function() use ($di) {
      $controller = new \Anax\Question\QuestionController();
      $controller->setDI($di);
      return $controller;
    });

$app = new \Anax\Kernel\CAnax($di);

// Set configuration for theme
$app->theme->configure(ANAX_APP_PATH . 'config/theme_tri.php');

// Set navbar
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar_tri.php');

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
$app->session;

$app->router->add('', function() use ($app) {
    $app->theme->setTitle("Zelda");
    $app->dispatcher->forward([
      'controller' => 'question',
      'action'     => 'firstPage',
      'params' => [
        'key' => 'home',
        'redirect' => '',
      ],
    ]);
    $app->dispatcher->forward([
      'controller' => 'users',
      'action'     => 'firstPage',
      // 'params' => [
      //   'key' => 'home',
      //   'redirect' => '',
      // ],
      ]);


});
$app->router->add('about', function() use ($app) {

  $content = $app->fileContent->get('about.md');
  $content = $app->textFilter->doFilter($content, 'shortcode, markdown');
  $app->theme->setTitle("Om Triforce");

  $app->views->add('index/page', [
    'content' => $content,
    ]);


  });
$app->router->add('question', function() use ($app) {
  $app->theme->setTitle("Frågor");

  $app->dispatcher->forward([
    'controller' => 'question',
    'action'     => 'view',
    'params' => [
      'key' => 'home',
      'redirect' => '',
    ],
  ]);
});

$app->router->add('source', function() use ($app) {
    $app->theme->addStylesheet('css/source.css');
    $app->theme->setTitle("Source-sida");

    $source = new \Mos\Source\CSource([
        'secure_dir' => '..',
        'base_dir' => '..',
        'add_ignore' => ['.htaccess'],
    ]);

    $app->views->add('index/source', [
        'content' => $source->View(),
    ]);
});

$app->router->add('users', function() use ($app) {

    $app->theme->setTitle("Users");
    $content = $app->fileContent->get('user.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('users/view-start', [
        'content' => $content,
    ]);
});


// $app->theme->addStylesheet('../webroot/css/flash.css');
$app->router->handle();
$app->theme->render();
