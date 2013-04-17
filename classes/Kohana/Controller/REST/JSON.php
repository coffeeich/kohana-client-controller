<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_Controller_REST_JSON extends Kohana_Controller_REST {

    /**
     * Sets REST headers
     */
    protected function headers() {
        $this->response->headers('Content-Type', ($this->request->is_ajax() ? 'application/json' : 'text/html') . '; charset=utf-8');
    }

    /**
     * Sets JSON response body
     * @param mixed $data value to be stringify
     */
    protected function response($data=array(), $options=JSON_FORCE_OBJECT) {
        $this->response->body(json_encode($data, $options));
    }

}