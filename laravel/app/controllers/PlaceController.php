<?php

/**
 * Places controller methods.
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PlaceController extends BaseController implements GenericControllers {
    
    public function all() {
        $placeService = $this->getService('Place');
        $places = $placeService->all();
        
        if($places !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $places);
        } else {
            return $this->jsonResponse('No places found.', self::HTTP_CODE_OK, []);
        }
    }

    public function create() {
        $placeService = $this->getService('Place');
        $place = $placeService->create(Input::all());
     
        if($place !== null) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not created model. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function destroy($id) {
        $placeService = $this->getService('Place');
        $success = $placeService->destroy($id);
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not destroy the place. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function edit($id) {
        $placeService = $this->getService('Place');
        $success = $placeService->edit($id, Input::all());
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the place. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function find($id) {
        $placeService = $this->getService('Place');
        
        $place = $placeService->find($id);
        
        if($place !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $place);
        } else {
            return $this->jsonResponse('Not place found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

}
