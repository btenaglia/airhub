<?php
namespace App\Services;

use App\Models\Mconfig;

/**
 * The config service logic here
 *
 */
//class MconfigService extends BaseService implements GenericServices {
class MconfigService extends BaseService {    
    public function getValueMconfig($fieldf, $valuef, $fieldreturn) {
        return Mconfig::getvaluesmconfig($fieldf, $valuef, $fieldreturn);
    }

    public function find($id) {
        return Mconfig::find($id);
    }
    
    public function updateValueMconfig($fieldf, $valuef, $fieldupdate,$valueupdate) {
    	  
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
