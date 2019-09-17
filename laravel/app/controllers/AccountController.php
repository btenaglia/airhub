<?php

use App\Models\User;
/**
 * Put all the registration, login and authentication process requests
 * and responses in this controller.
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class AccountController extends BaseController implements GenericControllers {

    public function create() {
        $accountService = $this->getService('Account');
        $userExists = $accountService->userExistsBE(Input::get('email'));
        $userExists2 = $accountService->userExists(Input::get('cell_phone'));
        
        if((!$userExists)&&(!$userExists2)) {
            $success = $accountService->createBackendUser(Input::all());
            
            if($success) {
                return $this->jsonSuccessResponse();
            } else {
                return $this->jsonResponse('Cannot create user. Try Again', self::HTTP_CODE_SERVER_ERROR);
            }
        } else {
            return $this->jsonResponse('User already exists with this email or cell phone.', self::HTTP_CODE_CONFLICT);
        }
    }
    
    public function createm() {
        $accountService = $this->getService('Account');
        $userExists = $accountService->userExists(Input::get('email'));
        $userExists2 = $accountService->userExists(Input::get('cell_phone'));
        
        if((!$userExists)&&(!$userExists2)) {
            $success = $accountService->createMobileUser2(Input::all());
            
            if($success) {
                // return $this->jsonSuccessResponse();
                return $this->jsonResponse('', self::HTTP_CODE_OK, Input::all());
                // return $this->jsonResponse('Cannot create user. Try Again', Input::all());
            } else {
                return $this->jsonResponse('Cannot create user. Try Again', self::HTTP_CODE_SERVER_ERROR);
            }
        } else {
            return $this->jsonResponse('User already exists with this email or cell phone.', self::HTTP_CODE_CONFLICT);
        }
    }
    
    public function old_create() {
        $accountService = $this->getService('Account');
        $userExists = $accountService->userExists(Input::get('email'));
        
        if(!$userExists) {
            $success = $accountService->createBackendUser(Input::all());
            
            if($success) {
                return $this->jsonSuccessResponse();
            } else {
                return $this->jsonResponse('Cannot create user. Try Again', self::HTTP_CODE_SERVER_ERROR);
            }
        } else {
            return $this->jsonResponse('User already exists with this email.', self::HTTP_CODE_CONFLICT);
        }
    }
    
    public function createMobileUser() {
        
        $accountService = $this->getService('Account');
        $userExists = $accountService->userExists(Input::get('email'));
        $userExists2 = $accountService->userExists(Input::get('cell_phone'));
       
       
        if((!$userExists)&&(!$userExists2)) {
            $success = $accountService->createMobileUser(Input::all());
            
            if($success) {
            
            return $this->jsonSuccessResponse();
            } else {
                return $this->jsonResponse('Cannot create user. Try Again', self::HTTP_CODE_SERVER_ERROR);
            }
        } else {
            return $this->jsonResponse('User already exists with this email or cell phone.', self::HTTP_CODE_CONFLICT);
        }
    }
    
    public function recoverMobileUser() {
    	  
    	  //if(!empty(Input::get('email'))){
    	  if(Input::get('email')!=''){
    	  
        $accountService = $this->getService('Account');
        $userExists = $accountService->userExists(Input::get('email'));
        
        if($userExists) {
             $success = $accountService->recoverPassword(Input::get('email'));
            return $this->jsonResponse('', self::HTTP_CODE_OK, $success);
            if($success) {
                return $this->jsonSuccessResponse();
            } else {
                return $this->jsonResponse('You can not recover the password. Try Again', self::HTTP_CODE_SERVER_ERROR);
            }
        } else {
            return $this->jsonResponse('The user with that email does not exist.', self::HTTP_CODE_CONFLICT);
        }
        
        } else {
        	  return $this->jsonResponse('You must send an email. Try Again', self::HTTP_CODE_CONFLICT);
        }	
    }

    public function login() {
        $credentials = Input::only('email', 'password');
        $accountService = $this->getService('Account');
        $userData = $accountService->authenticateByCredentials($credentials);
        
        if($userData === null) {
            return $this->jsonResponse('Invalid credentials.', self::HTTP_CODE_CONFLICT);
        } else {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $userData);
        }
    }
    public function validateToken($token){
        $accountService = $this->getService('Account');
        $user = $accountService->validateAccessToken($token);
        return $this->jsonResponse('', self::HTTP_CODE_OK, $user);
    }
    public function loginMobile() {
        $credentials = Input::only('email', 'password','id_onesignal','fcm_token_device');
        $accountService = $this->getService('Account');
        $userData = $accountService->authenticateAppUserByCredentials($credentials);
        
        if($userData === null) {
            return $this->jsonResponse('Invalid credentials.', self::HTTP_CODE_CONFLICT);
        } else {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $userData);
        }
    }
    
    public function loginMobileFB() {
        $credentials = Input::only('tokenfb','id_onesignal','fcm_token_device');
        $accountService = $this->getService('Account');
        $userData = $accountService->authenticateAppUserByFacebook($credentials);
                
        if($userData === null) {
            return $this->jsonResponse('Invalid credentials.', self::HTTP_CODE_CONFLICT);
        } else {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $userData);
        }
    }
    
    public function loginMobileFB2() {
        $credentials = Input::only('tokenfb');
        $accountService = $this->getService('Account');
        $userData = $accountService->authenticateAppUserByFacebook2($credentials);
                
        if($userData === null) {
            return $this->jsonResponse('Invalid credentials.', self::HTTP_CODE_CONFLICT);
        } else {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $userData);
        }
    }
    
    public function getCurrentUser(){
        $accountService = $this->getService('Account');
        
        $user = $accountService->getUserData();
        
        if($user === null){
            return $this->jsonResponse('User not found with the current token.', self::HTTP_CODE_UNAUTHORIZED);
        } else {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $user);
        } 
    }
    
    /**
     * @deprecated Implemented in the client-side
     */
    public function logout() {
        $accountService = $this->getService('Account');
        $success = $accountService->logout();
        
        if($success) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, ['success' => true]);
        } else {
            return $this->jsonResponse('Logout failed.', self::HTTP_CODE_SERVER_ERROR);
        }
    }

    public function all() {
        $accountService = $this->getService('Account');
        
        $users = User::with('getMember')->get();
        
        if($users !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $users);
        } else {
            return $this->jsonResponse('No users found.', self::HTTP_CODE_OK, []);
        }
    }
    public function all_mobile() {
              
        $users = User::with('getMember')->where('user_type','app_user')->get();
        
        if($users !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $users);
        } else {
            return $this->jsonResponse('No users found.', self::HTTP_CODE_OK, []);
        }
    }
    public function destroy($id) {
        $accountService = $this->getService('Account');
        $success = $accountService->destroy($id);
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not destroy the user. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function edit($id) {
        $accountService = $this->getService('Account');
        $success = $accountService->edit($id, Input::all());
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the user. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }
    
    public function editm($id) {
        $accountService = $this->getService('Account');
        $success = $accountService->edit($id, Input::all());
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the user. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }
    
    public function modify() {
        $accountService = $this->getService('Account');
        
        $user = $accountService->getUserData();
        
        $id =  $user->id;
        
        $success = $accountService->edit($id, Input::all());
        
        if($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the user. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function find($id) {
        $accountService = $this->getService('Account');
        
        $account = $accountService->find($id);
        
        if($account !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $account);
        } else {
            return $this->jsonResponse('Not user found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }
    
    public function SendPushtoUser($id) {
        $accountService = $this->getService('Account');
        $pushService = $this->getService('Push');
        $user = $accountService->find($id);
        $data = array('time'=>time()); 
        
        if($user !== null) {
        	  $iddevice = $user->id_onesignal;
        	  $iddevice_firebase = $user->getIdFirebase();
            if(!empty($iddevice)){
             try {
              $pushService->sendtoDevice('This is a test message for push service!',$iddevice);
              if(!empty($iddevice_firebase))$pushService->sendtoDeviceFCM('Notify','This is a test message for push service!',$iddevice_firebase,$data);
             } catch (\Exception $ex) {
              return $this->jsonResponse('Onesignal API error.', self::HTTP_CODE_SERVER_ERROR, $ex);
             } 
             
             return $this->jsonResponse('', self::HTTP_CODE_OK, $user);
            }else{
             return $this->jsonResponse('No id Onesignal defined.', self::HTTP_CODE_SERVER_ERROR, []);
            }	 
        } else {
            return $this->jsonResponse('Not user found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
        
    }
    
    public function testapiAM(){
    	$mailService = $this->getService('Mail');
      return $mailService->sendTest();
    }

 
  	
}
