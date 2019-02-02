<?php
namespace App\Services;

use App\Models\Profile;

/**
 * TODO Comment of component here!
 *
 */
class ProfileService extends BaseService implements GenericServices {
    public function all() {
        return Profile::findAllActives('App\Models\Profile');
        //return Profile::all_custom();
        
        //return Profile::all('App\Models\Profile');
        //return Profile::all();
    }

    public function create($input) {
        $profile = new Profile();
        $profile->setName($input['name']);
        $profile->setPrice($input['price']);
        if(isset($input['type']))
           $profile->setTypes($input['type']);
        else
           $profile->setTypes(0);
        if(isset($input['hours']))$profile->setHours($input['hours']);
        if(isset($input['seats']))$profile->setSeats($input['seats']);
        
        $success = $profile->save();
        
        if($success) {
            return $profile;
        } else {
            return null;
        }
    }

    public function destroy($id) {
        $profile = $this->find($id);
        $profile->setActive(false);
        
        return $profile->update();
    }

    public function edit($id, $input) {
        $profile = $this->find($id);
        $profile->setName($input['name']);
        $profile->setPrice($input['price']);
        $profile->setTypes($input['type']);
        if(isset($input['hours']))$profile->setHours($input['hours']);
        if(isset($input['seats']))$profile->setSeats($input['seats']);
        
        $success = $profile->update();
        
        if($success) {
            return $profile;
        } else {
            return null;
        }
    }

    public function find($id) {
        return Profile::find($id);
        //return Profile::find_custom($id);
        
        //$out = Profile::find_custom($id);
        //return $out[0];
    }
    
    public function find_last() {
        $out = Profile::find_custom_last();
        return $out[0];
    }
    
    public function getPriceWithFlight($flightid=null){
    	if($flightid!=null){
    	
    	$flight = $this->getService('Flight')->find($flightid);
    	//print_r($flight);
    	$fcreated = $flight->created_at;
    	$nseats_tmp = $this->getService('Book')->findByFlight($flightid);
    	$nseats = count($nseats_tmp);
    	
      }else{
      	
      $fcreated = time();	
      $nseats = 0;
      	
      }	
    	
    	$switch_price = false;
    	$price = 0;
    	
    	$profile = $this->find_last();
    	$type = $profile->type;
    	if($type==0){
    	  $seats = $profile->seats;
    	  if($nseats+1<=$seats){
    	  	$price = $profile->price;
    	  	$switch_price = true;
    	  }	
    	}else if($type==1){
    	  $hours = $profile->hours;
    	  $limit_hours = strtotime($fcreated) + $hours*3600;
    	  $ahora = time();
    	  if($ahora<=$limit_hours){
    	  	$price = $profile->price;
    	  	$switch_price = true;
    	  }
    	}
    	
    	if($switch_price)
    	 return $price;
    	else 
    	 return false;
    
    }	
    
}
