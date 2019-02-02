<?php

/**
 * Planes controllers methods
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PlaneController extends BaseController implements GenericControllers {
    public function all() {
        $planeService = $this->getService('Plane');
        $planes = $planeService->all();
        
        if($planes !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $planes);
        } else {
            return $this->jsonResponse('No planes found.', self::HTTP_CODE_OK, []);
        }
    }

    public function create() {
        $planeService = $this->getService('Plane');
        $plane = $planeService->create(Input::all());
        
        if($plane !== null) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not created model. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function destroy($id) {
        $planeService = $this->getService('Plane');
        $success = $planeService->destroy($id);
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not destroy the plane. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function edit($id) {
        $planeService = $this->getService('Plane');
        $success = $planeService->edit($id, Input::all());
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the plane. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function find($id) {
        $planeService = $this->getService('Plane');
        
        $plane = $planeService->find($id);
        
        if($plane !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $plane);
        } else {
            return $this->jsonResponse('Not plane found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }
}
