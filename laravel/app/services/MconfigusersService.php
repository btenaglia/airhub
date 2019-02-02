<?php
namespace App\Services;

use App\Models\Mconfigusers;

/**
 * The config service logic here
 *
 */
//class MconfigService extends BaseService implements GenericServices {
class MconfigusersService extends BaseService {    
    public function getValueMconfigusers($iduser, $param) {
        return Mconfigusers::getvaluesmconfigusers($iduser, $param);
    }

    public function find($id) {
        return Mconfigusers::find($id);
    }
    
    public function updateValueMconfigusers($fieldf, $valuef, $fieldupdate,$valueupdate) {
    	  
    }	
    
    /*
    //IF USE GenericServices
    public function create($input){
    }
    
    public function edit($id, $input){
    }
    
    public function destroy($id){
    }
    
    public function all(){
    }
    
    public function find($id){
    }
    */
    
}
