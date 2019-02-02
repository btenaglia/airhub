<?php
namespace App\Services;

use App\Models\Plane;
use Illuminate\Support\Facades\DB;

/**
 * Services related to the planes
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PlaneService extends BaseService implements GenericServices {
    
    public function all() {
        return Plane::findAllActives('App\Models\Plane');
    }

    public function create($input) {
        $plane = new Plane();
        $plane->setIdentifier($input['identifier']);
        $plane->setName($input['name']);
        $plane->setType($input['type']);
        $plane->setSeatsLimit($input['seats_limit']);
        $plane->setWeightLimit($input['weight_limit']);
        
        $success = $plane->save();
        
        if($success) {
            return $plane;
        } else {
            return null;
        }
    }

    public function destroy($id) {
    	
    	  $inflight = DB::table('flights')->where('plane_id', $id)->first();
    	  //$inflight = Plane::getFlights();
    	  
    	  if(!$inflight){
    	
        $plane = $this->find($id);
        $plane->setActive(false);
        
        return $plane->update();
        
        }else{
        	
        return false;	
        	
        }
    }

    public function edit($id, $input) {
        $plane = $this->find($id);
        $plane->setIdentifier($input['identifier']);
        $plane->setName($input['name']);
        $plane->setType($input['type']);
        $plane->setSeatsLimit($input['seats_limit']);
        $plane->setWeightLimit($input['weight_limit']);
        
        $success = $plane->update();
        
        if($success) {
            return $plane;
        } else {
            return null;
        }
    }

    public function find($id) {
        return Plane::find($id);
    }
}
