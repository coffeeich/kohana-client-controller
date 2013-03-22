<?php

return array(
    'loader'      => '',      // the script which should initialize all require.js environment, i.e. 'frontend/loader'
    'require'     => '',      // a require.js script path for development mode, i.e. 'frontend/require'
    'production'  => '',      // a production builds path, which contains a require.js builds with the almond stubs, i.e. 'frontend/builds'
    'development' => '',      // a development page path with the web pages entry points, i.e. 'frontend/pages'
    'legacy'      => array(), // a web page configutation, i.e.:
                              //  array(
                              //      'use_build' => true,                      - use a script from builds dir
                              //      'page'      => 'legacy_support',          - use a script wich main name is 'welcome_index'
                              //      'theme'     => 'default',                 - use a theme named 'default' (it's in '{pages,builds}/skins/legacy_support/{less,css}/default.{less,css}')
                              //  ),
    'pages'       => array(), // a list of web pages configutation, it's hash, i.e.:
                              //  array(
                              //      'Welcome' => array(                       - controller name (with directory subpath)
                              //          'index' => array(                     - action name
                              //              'use_build' => true,              - use a script from builds dir
                              //              'page'      => 'welcome_index',   - use a script wich main name is 'welcome_index'
                              //              'theme'     => 'default',         - use a theme named 'default' (it's in '{pages,builds}/skins/welcome_index/{less,css}/default.{less,css}')
                              //          ),
                              //      ),
                              //  ),
);