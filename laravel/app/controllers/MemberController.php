<?php

/**
 * Member controller methods.
 *
 */
use App\Models\Member;
use App\Models\Mconfigusers;
 
class MemberController extends BaseController implements GenericControllers {
    
    

    public function all() {
        $members = new Member();
        $places = $members->all();
        
        if($places !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $places);
        } else {
            return $this->jsonResponse('No places found.', self::HTTP_CODE_OK, []);
        }
    }

    public function create() {
        $member = new Member();
        $newMember = $member->create(Input::all());
     
        if($newMember !== null) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not created model. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function destroy($id) {
        $member = new Member();
        $success = $member->destroy($id);
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not destroy the place. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function edit($id) {
        $member = new Member();
        $memberupdate = $member->find($id);
        $data = Input::all();
        $memberupdate->description = $data['description'];
        $memberupdate->discount = $data['discount'];
        $success = $memberupdate->update();
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the place. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function find($id) {
        $member = new Member();
        
        $member = $member::find($id);
        
        if($member !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $member);
        } else {
            return $this->jsonResponse('Not place found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }
}