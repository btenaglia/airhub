<?php

namespace App\Services;

//use Berkayk\OneSignal\OneSignalServiceProvider as  OneSignal;

//Firebase
use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

class PushService extends BaseService {
	
	  private $App_ID = "7b671c99-0f83-4b53-9ef0-c51371a5547c";
	  private $Rest_Api_Key = "ZmUxMzg2ZTAtZTNhNi00ZTg1LWFhZTEtZTEzNDJiZDViODlk";
	  private $User_Auth_Key = "N2RhZmE4MzktM2Y5YS00N2FlLWEyZGYtNGMxMTcyNDhmYzY1";
	  private $Server_Key_Firebase = "AIzaSyBbFz2cYeOWbrtHrLCjPnvZ6MCsNXT8gW8";
    
    public function sendtoAll($message) {
      $client = new \Berkayk\OneSignal\OneSignalClient($this->App_ID,$this->Rest_Api_Key,$this->User_Auth_Key);
      $result = $client->sendNotificationToAll($message);
      return $result;
    }
    
    public function sendtoDevice($message,$iddevice) {
      $client = new \Berkayk\OneSignal\OneSignalClient($this->App_ID,$this->Rest_Api_Key,$this->User_Auth_Key);
      $result = $client->sendNotificationToUser($message,$iddevice);
      return $result;
    }
    
    public function sendtoDeviceFCM($title,$messagetxt,$iddevice,$data) {
    	$client = new Client();
      $client->setApiKey($this->Server_Key_Firebase);
      $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
      
      $message = new Message();
      $message->addRecipient(new Device($iddevice));
      /*$message
          ->setNotification(new Notification($title, $messagetxt))
          ->setData(['key' => 'value'])
      ;*/
      $message
          ->setNotification(new Notification($title, $messagetxt))
          ->setData($data)
      ;
      
      $response = $client->send($message);
    }	
    
}
