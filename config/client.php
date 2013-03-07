<?php

return array(
    'loader'      => '', //'frontend/loader',   script which should initialize all require.js environment
    'require'     => '', //'frontend/require',  require.js script path for development mode
    'production'  => '', //'frontend/builds',   production builds path, which contains require.js builds with almond stubs
    'development' => '', //'frontend/pages',    development paged path with web pages entry points
    'pages'       => array(),               //  a list of web pages configutation, it's hash map like:
                                            //  'Welcome' => array(                         - controller name (with directory subpath)
                                            //      'index' => array(                       - action name
                                            //          'use_build'  => true,               - use a script from builds dir
                                            //          'page'       => 'welcome_index',    - use a script wich main name is 'welcome_index'
                                            //          'theme'      => 'default',          - use a theme named 'default' (it's in '{pages,builds}/skins/welcome_index/{less,css}/default.{less,css}')
                                            //      ),
                                            //  ),
);