<?php

/**
 * Config controller methods.
 *
 */
use App\Models\User;
use App\Models\Mconfigusers;
 
class ConfigController extends BaseController implements GenericControllers {
    
    public function all() {
    }

    public function create() {
    }

    public function destroy($id) {
    }

    public function edit($id) {
    }

    public function find($id) {
    }
    
    public function cview() {
    	$id = $this->getCurrentUser()->getData()->data->id;
    	
    	$configService = $this->getService('Mconfigusers');
    	
    	$value = $configService->getValueMconfigusers($id,'push');
    	if(count($value)>0){$result = $value[0]->value;}else{$result = 0;}
    	$value2 = $configService->getValueMconfigusers($id,'txt');
    	if(count($value2)>0){$result2 = $value2[0]->value;}else{$result2 = 0;}
    	$value3 = $configService->getValueMconfigusers($id,'email');
    	if(count($value3)>0){$result3 = $value3[0]->value;}else{$result3 = 0;}
    	
    	$results = array('push'=>$result,'txt'=>$result2,'email'=>$result3);
      return $this->jsonResponse('', self::HTTP_CODE_OK, $results);
    }	
    
    public function cedit() {
    
    $id = $this->getCurrentUser()->getData()->data->id;	
    	
    //$input = Input::json()->all();
    $input = Input::only('push', 'txt', 'email');
    //$input = Input::all();
  	//dd(DB::getQueryLog());exit(0);
  	
    $change = 0;
    
  	foreach($input as $key => $value){
  	
  	switch($key){
  		case 'push':
  		case 'txt':
  		case 'email':
  		 if($value!=null){
  		 $Config = Mconfigusers::whereRaw('parameter = ? and iduser = ?',array($key,$id));
  		 if($Config->count() == 0){
  		 	
  		 //ADD
  		 $newconfig = new Mconfigusers;
  		 $newconfig->parameter = $key;
       $newconfig->iduser = $id;
       $newconfig->value = $value;
       $newconfig->save();
       
       }else{
       
       //EDIT
       $econfig = Mconfigusers::find($Config->first()->id);
    	 //$econfig->parameter = $key;
    	 $econfig->value = $value;
       $econfig->save();
       
       }
  		 $change = 1;
  		 }
  		 break; 
  		default:
  		 break; 
    }
    
    }
    
    //var_dump($input);exit(0);
    
    
    //print_r($input);exit(0);
    
    if($change == 1){
    	//parametros cambiados
    	//$results = array('push'=>$result,'txt'=>$result2,'email'=>$result3);
      $results = array('message' => 'Success.');
      return $this->jsonResponse('', self::HTTP_CODE_OK, $results);
    }else{
    	//no parametros cambiados
    	return $this->jsonResponse('No parameters defined: push, txt or email.', self::HTTP_CODE_CONFLICT);
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

}
