<?php

/**
 * Member controller methods.
 *
 */

use App\Models\Member;
use App\Models\Mconfigusers;
use App\Models\Message;
 
class MemberController extends BaseController implements GenericControllers {
    
    

    public function all() {
        
        $members = new Member();
        $members = $members->all();
        
        if($members !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $members);
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
    public function notification() {
        $notificationData = Input::all();
        $users = Member::find($notificationData['member_id'])->getUsers;
       
        $pushService = $this->getService('Push');
        $asd = new Message();
       foreach($users as $user){
        try{
            if($user->id_onesignal !== null && $user->id_onesignal !== '')
            $pushService->sendtoDevice($notificationData['message'],$user->id_onesignal);

        }
        catch(Exception $e){
            echo $e;
        }
       }
       $msg = new Message();
       $msg->message = $notificationData['message'];
       $msg->member_id = $notificationData['member_id'];
       $msg->save();
       return $this->jsonResponse('', self::HTTP_CODE_OK, $msg);
     
    }
}
