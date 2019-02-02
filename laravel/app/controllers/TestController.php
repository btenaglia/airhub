<?php

/*use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;*/

//namespace App\Services;

use App\Models\User;

class TestController extends BaseController implements GenericControllers {

    public function create() {
    }

    public function destroy($id) {
    }

    public function edit($id) {
    }
    
    public function find($id) {
    }
    
    public function all() {
    }
    
    public function test(){
    	
    	//test1
    	
    	//$pushService = $this->getService('Push');
    	//$pushService->sendtoAll('Este es un mensaje de prueba Global. Confirmar si lo recibiste.');    
    	
    	//$bookings = AppPayment::bookingsByPayment(30);
    	
    	//var_dump($bookings[0]->idOnesignal);
    	//var_dump($bookings[0]->user_id);
    	
    	
    	
    	
      //test2    	
      
    	/*$server_key = 'AIzaSyDdFkkM8hELRhEoDuLnSW7FtotCSq_2bfw';
      $client = new Client();
      $client->setApiKey($server_key);
      $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
      
      $message = new Message();
      $message->addRecipient(new Device('textesttest'));
      $message
          ->setNotification(new Notification('some title', 'some body'))
          ->setData(['key' => 'value'])
      ;
      
      $response = $client->send($message);*/
      //var_dump($response->getStatusCode());
      //var_dump($response);
      
      
      //test3
        
        $id = 20;
      
        $accountService = $this->getService('Account');
        $pushService = $this->getService('Push');
        $user = $accountService->find($id);
        
        if($user !== null) {
        	  $iddevice = $user->fcm_token_device;
            if(!empty($iddevice)){
             
             try {
              $result = $pushService->sendtoDeviceFCM('Notify','This is a test message for push service (FCM)!',$iddevice);
             } catch (\Exception $ex) {
              return $this->jsonResponse('FCM API error.', self::HTTP_CODE_SERVER_ERROR, $ex);
             } 
             
             return $this->jsonResponse('Success', self::HTTP_CODE_OK, $result);
            }else{
             return $this->jsonResponse('No id FCM defined.', self::HTTP_CODE_SERVER_ERROR, []);
            }	 
        } else {
            return $this->jsonResponse('Not user found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
        
        
        
        

    }
    
    public function testauth(){
    	//test4
    	//$user = $this->getCurrentUser();
    	/*$id = 20;
    	$user = User::findAppUserById($id);
      $iddevice = $user->getIdFirebase();
      echo $iddevice;*/
      
      //test5 ***********
          //$user = $this->getCurrentUser();
      //$user = User::find(20);
      /*$usert = (array)$user;
    	print_r( $usert );*/
    	//echo $user->getIdFirebase();
    	//var_dump($user->getCompleteName());
    	
    	   //$rest = $user->getData();
    	   //var_dump($rest->data->id);
    	
    	/*$user = Auth::User();
    	print_r( $user );*/
    	
    	/*$configService = $this->getService('Mconfigusers');
    	$cost = $configService->getValueMconfigusers($id,'param1');
    	$costff = $cost[0]->value;
      return $this->jsonResponse('', self::HTTP_CODE_OK, ["paramreturn" => $costff]);*/
      
      
      //test6 ****
      $user = $this->getCurrentUser()->getData()->data->id;
      //$rest = $user->getData();
    	echo $user;
       
      
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
  	
}
