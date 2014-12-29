<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'home'  => [
            'text'  => '<i class="fa fa-home"></i> Home',
            'url'   => '',
            'title' => 'Hem',
        ],

        // This is a menu item
        'question'  => [
            'text'  => '<i class="fa fa-question"></i> Frågor',
            'url'   => 'question',
            'title' => 'Frågor',
        ],
        // This is a menu item
        'tags'  => [
            'text'  => '<i class="fa fa-tags"></i> Taggar',
            'url'   => 'question/allTags',
            'title' => 'Taggar',
        ],
        'users'  => [
          'text'  => '<i class="fa fa-users"></i> Användare',
          'url'   => 'users',
          'title' => 'Användare',
        ],

        // This is a menu item
        'about' => [
            'text'  =>'<i class="fa fa-align-left"></i> Om sidan',
            'url'   =>'about',
            'title' =>'Om sidan',
        ],
                // This is a menu item
        'login' => [
            'text'  =>'<i class="fa fa-lock"></i> Login',
            'url'   =>'users/login',
            'title' =>'Inloggning',
        ],
                // This is a menu item
        'medlem' => [
            'text'  =>'<i class="fa fa-user"></i> Bli Medlem',
            'url'   =>'users/add',
            'title' =>'Medlem'
        ]
    ],
 
    // Callback tracing the current selected menu item base on scriptname
    'callback' => function($url) {
        if ($url == $this->di->get('request')->getRoute()) {
            return true;
        }
    },

    // Callback to create the urls
    'create_url' => function($url) {
        return $this->di->get('url')->create($url);
    },
]; 