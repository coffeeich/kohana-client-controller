<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_Controller_REST extends Controller {

    /**
     * Executes the given action via parent mrthod call. Sets content type header, catches
     * execution exceptions and returns them in response with specified error status or 500 as default error status.
     *
     * @return  Response
     */
    public function execute() {
        $this->headers();

        try {
            parent::execute();
        } catch (Exception $ex) {
            $code = $ex->getCode();

            $this->response->status(array_key_exists($code, Response::$messages) ? $code : 500);

            $this->response(Kohana::$environment === Kohana::PRODUCTION ?
                array(
                    'message' => $ex->getMessage(),
                )
                :
                array(
                    'code'     => $ex->getCode(),
                    'file'     => $ex->getFile(),
                    'line'     => $ex->getLine(),
                    'message'  => $ex->getMessage(),
                    'previous' => $ex->getPrevious(),
                    'trace'    => $ex->getTrace(),
                ));
        }

        return $this->response;
    }

    /**
     * Sets REST headers
     */
    abstract protected function headers();

    /**
     * Sets REST response body
     * @param mixed $data value to be a response
     */
    abstract protected function response($data=NULL, $options=NULL);

    /**
     * Overrides routing params
     * @param Route $route
     * @param array $params
     * @param Request $request
     * @return array
     */
    static public function override(Route $route, array $params, Request $request) {
        $headers = $request->headers();

        $method = array_key_exists('x-http-method-override', $headers)
            ? strtoupper($headers['x-http-method-override'])
            : $request->method();

        // checkout json POST data
        if (in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE')) &&
            strpos($request->headers('content-type'), 'application/json') !== FALSE) {

            $request->post(
                $_POST = Kohana::sanitize((array) json_decode(file_get_contents('php://input'), TRUE))
            );
        }

        $params['action'] = strtolower($method) . '_' . $params['action'];

        return $params;
    }

}