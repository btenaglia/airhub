<?php

class BaseController extends Controller {
    const HTTP_CODE_OK = 200;
    const HTTP_CODE_SERVER_ERROR = 500;
    const HTTP_CODE_UNAUTHORIZED = 401;
    const HTTP_CODE_CONFLICT = 409;

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }
    
    /**
     * Get a new instance of the specified service
     *
     * @param string $servicePrefix The service name
     *
     * @return Service
     */
    protected function getService($servicePrefix) {
        return App::make($servicePrefix . 'Service');
    }
    
    /**
     * Create and return a json response
     * @param type $errorMessage
     * @param type $data
     * @param type $httpCode
     */
    protected function jsonResponse($errorMessage, $httpCode, $data = array()) {
        return Response::json(
            [
                'error' => $errorMessage, 
                'data' => $data
            ],
            $httpCode
        , [], JSON_NUMERIC_CHECK);
    }
    
    /**
     * Create and return a json response
     */
    protected function jsonSuccessResponse() {
        return Response::json(
            [
                'error' => '', 
                'data' => [
                    "success" => true
                ]
            ],
            self::HTTP_CODE_OK
        );
    }
}
