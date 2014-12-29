<?php
/**
 * Config-file for Anax, theme related settings, return it all as array.
 *
 */
return [

    'views' => [
        [
            'region' => 'header',
            'template' => 'tri/header',
            'data' => [],
            'sort' => -1
        ],
        [
            'region' => 'navbar',
            'template' => [
                'callback' => function() {
                    return $this->di->navbar->create();
                },
            ],
            'data' => [],
            'sort' => -1
        ],
        ['region' => 'footer', 'template' => 'tri/footer', 'data' => [], 'sort' => -1],
    ],

    /** 
     * Data to extract and send as variables to the main template file.
     */
    'data' => [

        // Language for this page.
        'lang' => 'sv',

        // Append this value to each <title>
        'title_append' => ' | Triforce',

        // Stylesheets
        //'stylesheets' => ['css/style.css', 'css/navbar.css'], 
        'stylesheets' => ['css/style.css', 'css/navbar_tri.css'],
        
        // Inline style
        'style' => null,

        // Favicon
        'favicon' => 'favicon.ico',

        // Path to modernizr or null to disable
        'modernizr' => 'js/modernizr.js',

        // Path to jquery or null to disable
        'jquery' => '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js',

        // Array with javscript-files to include
        'javascript_include' => [],

        // Use google analytics for tracking, set key or null to disable
        'google_analytics' => null,
    ],
];

?> 