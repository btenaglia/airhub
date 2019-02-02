<?php
namespace App\Services;

use App\Models\Flight;

/**
 * All the flights logic here.
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class FlightService extends BaseService implements GenericServices {
    
    public function all() {
        return Flight::findAllActives('App\Models\Flight')->toArray();
    }
    
    public function allWithRelationsAttrs() {
        return Flight::allWithRelationsAttrs();
    }
    
    public function futureFlights() {
        return Flight::futureFlights();
    }
    
    public function passedFlights() {
        return Flight::passedFlights();
    }

    public function create($input) {
        $flight = new Flight();
        $flight = $this->createFlight($flight, $input);
        
        $success = $flight->save();
        
        if($success) {
            return $flight;
        } else {
            return null;
        }
    }
    
    /**
     * Create a flight (called only from create and edit functions)
     * @param type $flight new or fetched from the DB
     * @param type $input $_REQUEST
     * @return $flight
     */
    private function createFlight($flight, $input) {
        $flight->setDepartureDate($input['departure_date']);
        $flight->setStatus($input['status']);
        if($this->checkIfFieldExists($input, 'paramvalueamount'))$flight->setAmount($input['paramvalueamount']);
        
        if($this->checkIfFieldExists($input, 'departure_time')) {
            
            $flight->setDepartureTime($input['departure_time']);
            $flight->setDepartureMinTime(null);
            $flight->setDepartureMaxTime(null);
            
        }else if($this->checkIfFieldExists($input, 'departure_min_time') && $this->checkIfFieldExists($input, 'departure_max_time')){
            
            $flight->setDepartureTime(null);
            $flight->setDepartureMinTime($input['departure_min_time']);
            $flight->setDepartureMaxTime($input['departure_max_time']);
            
        }else{
            return null;
        }
        
        //check if the plane was settled
        if($this->checkIfFieldExists($input, 'plane_id')) {
            $planeService = $this->getService('Plane');
            $plane = $planeService->find($input['plane_id']);
            $flight->setPlane($plane);
        }
        
        //assign the origin and destination place
        $placeService = $this->getService('Place');
        $origin = $placeService->find($input['origin']);
        $destination = $placeService->find($input['destination']);
        $flight->setOrigin($origin);
        $flight->setDestination($destination);
        
        //set the current user (fetched with the token request)
        $user = $this->getCurrentUser();
        $flight->setUser($user);
        
        return $flight;
    }

    public function destroy($id) {
        $flight = $this->find($id);
        $flight->setActive(false);
        
        return $flight->update();
    }

    public function edit($id, $input) {
        $flight = $this->find($id);
        $flight = $this->createFlight($flight, $input);
        
        $success = $flight->update();
        
        if($success) {
            return $flight;
        } else {
            return null;
        }
    }
    
    public function setPlane($id, $input) {
        $planeService = $this->getService('Plane');
        $plane = $planeService->find($input['plane_id']);
        
        $flight = $this->find($id);
        $flight->setPlane($plane);
        
        $success = $flight->update();
        
        if($success) {
            return $flight;
        } else {
            return null;
        }
    }

    public function find($id) {
        return Flight::find($id);
    }
    
    public function approve($id) {
        $flight = $this->find($id);
        $flight->setStatus('scheduled');
        
        $success = $flight->update();
        
        if($success) {
            
            $mailService = $this->getService('Mail');
            $userService = $this->getService('Account');
            
            $pushService = $this->getService('Push');
            
            $origin = $flight->getOrigin->name.' ('.$flight->getOrigin->name.')';
    	      $destination = $flight->getDestination->name.' ('.$flight->getDestination->name.')';
    	      $time = $flight->departure_date.' '.($flight->departure_time==''||$flight->departure_time==null?$flight->departure_min_time.' - '.$flight->departure_max_time:$flight->departure_time);
    	      $time_arrive = '';
    	      $plane = $flight->getPlane->name.' - '.$flight->getPlane->type.' ('.$flight->getPlane->identifier.')';
            
            $users = $this->getUsersOfFlight($flight->getId());
            foreach ($users as $userRaw){
                
                $user = $userService->find($userRaw->id);
                //$mailService->sendApprovedFlight($flight, $user);   
                
                //Notify
                //Push
                
                $book = $user->getBookByFlight($userRaw->id,$flight->id);
                
                $data = array('time'=>$time,
                              'time_arrive'=>$time_arrive,
                              'route'=>'from: '.$origin.' to: '.$destination,
                              'price'=>$book->amount);
                $iddevice = $user->getIdFirebase();
                if(!empty($iddevice))$pushService->sendtoDeviceFCM('Notify','Flight has been approved.',$iddevice,$data);
                //Email
                 
            }    
            
            return $flight;
        } else {
            return null;
        }
    }
    
    public function test($id) {
        $flight = $this->find($id);
       
        if(true) {
            $userService = $this->getService('Account');
            
            $origin = $flight->getOrigin->name.' ('.$flight->getOrigin->name.')';
    	      $destination = $flight->getDestination->name.' ('.$flight->getDestination->name.')';
    	      $time = $flight->departure_date.' '.($flight->departure_time==''||$flight->departure_time==null?$flight->departure_min_time.' - '.$flight->departure_max_time:$flight->departure_time);
    	      $time_arrive = '';
    	      $plane = $flight->getPlane->name.' - '.$flight->getPlane->type.' ('.$flight->getPlane->identifier.')';
            $users = $this->getUsersOfFlight($flight->getId());
            
            $ResultsG = array();
            
            foreach ($users as $userRaw){
                $user = $userService->find($userRaw->id);
                //$mailService->sendApprovedFlight($flight, $user);   
                
                //Notify
                //Push
                
                $book = $user->getBookByFlight($userRaw->id,$flight->id);
                
                $data = array('user'=>$userRaw->id,
                              'time'=>$time,
                              'time_arrive'=>$time_arrive,
                              'route'=>'from: '.$origin.' to: '.$destination,
                              'price'=>$book);
                $iddevice = $user->getIdFirebase();
                $ResultsG[] = $data;
                //Email
                 
            }    
            
            return $ResultsG;
        } else {
            return null;
        }
    }
    
    public function cancel($id) {
        $flight = $this->find($id);
        $flight->setStatus('proposed');
        
        $success = $flight->update();
        
        if($success) {
            
            $mailService = $this->getService('Mail');
            $userService = $this->getService('Account');
            
            $users = $this->getUsersOfFlight($flight->getId());
            foreach ($users as $userRaw){
                
                $user = $userService->find($userRaw->id);
                //$mailService->sendApprovedFlight($flight, $user);    
            }    
            
            return $flight;
        } else {
            return null;
        }
    }
    
    private function getUsersOfFlight($id){
        return Flight::userByFlight($id);
    }

    public function findWithRelationsAttrs($id) {
        return Flight::findWithRelationsAttrs($id);
    }
}
