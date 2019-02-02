<?php

/**
 * Setup controller methods.
 *
 */
class SetupController extends BaseController implements GenericControllers {
    
    public function all() {
    }

    public function create() {
    }

    public function destroy($id) {
    }

    public function edit($id) {
        $setupService = $this->getService('Setup');
        $success = $setupService->edit($id, Input::all());
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the setup. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function find($id) {
        $setupService = $this->getService('Setup');
        
        $setup = $setupService->find($id);
        
        if($setup !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $setup);
        } else {
            return $this->jsonResponse('Not setup found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

}
