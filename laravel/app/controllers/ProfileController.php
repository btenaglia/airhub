<?php

/**
 * Profiles controller methods.
 *
 */
class ProfileController extends BaseController implements GenericControllers {
    
    public function all() {
        $profileService = $this->getService('Profile');
        $profiles = $profileService->all();
        
        if($profiles !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $profiles);
        } else {
            return $this->jsonResponse('No profiles found.', self::HTTP_CODE_OK, []);
        }
    }

    public function create() {
        $profileService = $this->getService('Profile');
        $profile = $profileService->create(Input::all());
        
        if($profile !== null) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not created model. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function destroy($id) {
        $profileService = $this->getService('Profile');
        $success = $profileService->destroy($id);
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not destroy the profile. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function edit($id) {
        $profileService = $this->getService('Profile');
        $success = $profileService->edit($id, Input::all());
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the profile. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function find($id) {
        $profileService = $this->getService('Profile');
        
        $profile = $profileService->find($id);
        
        if($profile !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $profile);
        } else {
            return $this->jsonResponse('Not profile found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

}
