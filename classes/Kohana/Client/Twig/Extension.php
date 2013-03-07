<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Client_Twig_Extension extends Twig_Extension {

    public function getFunctions() {
        return array(
            'inject_controller_script' => new Twig_Function_Function('Client::inject_controller_script', array('is_safe' => array('html'))),
            'inject_theme_styles'      => new Twig_Function_Function('Client::inject_theme_styles', array('is_safe' => array('html'))),
        );
    }

    public function getName() {
        return 'kohana_client_twig_extension';
    }

}
