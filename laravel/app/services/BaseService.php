<?php

namespace App\Services;

/**
 * Base methods for a service
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class BaseService {

    /**
     * Get a new instance of the specified service
     *
     * @param string $servicePrefix The service name
     *
     * @return Service
     */
    protected function getService($servicePrefix) {
        return \App::make($servicePrefix . 'Service');
    }

    /**
     * Check if the input is there
     * @param type $input $_REQUEST
     * @param type $field current field string
     * @return boolean 
     */
    protected function checkIfFieldExists($input, $field) {
        if (\Input::has($field)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Current user
     * @return $user
     */
    protected function getCurrentUser() {
        try {
            if (!$user = \JWTAuth::parseToken()->authenticate()) {
                return null;
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return null;
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return null;
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return null;
    }
        
        return $user;
    }

}
