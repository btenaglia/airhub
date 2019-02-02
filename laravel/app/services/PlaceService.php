<?php
namespace App\Services;

use App\Models\Place;
use Illuminate\Support\Facades\DB;

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PlaceService extends BaseService implements GenericServices {
    public function all() {
        return Place::findAllActives('App\Models\Place');
    }

    public function create($input) {
        $place = new Place();
        $place->setName($input['name']);
        $place->setShortName($input['short_name']);
        
        $success = $place->save();
        
        if($success) {
            return $place;
        } else {
            return null;
        }
    }

    public function destroy($id) {
    	
    	  $inflight = DB::table('flights')->where('origin', $id)->first();
    	  $inflight2 = DB::table('flights')->where('destination', $id)->first();
    	  //$inflight = Place::getOriginFlight();
    	  //$inflight2 = Place::getDestinationFlight();
    	  
    	  if(!($inflight||$inflight2)){
    	
        $place = $this->find($id);
        $place->setActive(false);
        
        return $place->update();
        
        }else{
        	
        return false;	
        	
        }	
    }

    public function edit($id, $input) {
        $place = $this->find($id);
        $place->setName($input['name']);
        $place->setShortName($input['short_name']);
        
        $success = $place->update();
        
        if($success) {
            return $place;
        } else {
            return null;
        }
    }

    public function find($id) {
        return Place::find($id);
    }
}
