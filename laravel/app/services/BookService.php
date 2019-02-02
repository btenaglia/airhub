<?php
namespace App\Services;

use App\Models\Book;
use App\Models\AppPayment;

/**
 * The book service logic here
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class BookService extends BaseService implements GenericServices {
    public function all() {
        return Book::allWithFlights();
    }

    public function create($input) {
        
        //check if the payment is allwright
        $paymentService = $this->getService('Payment');
        $paymentService->setPayment($input['payment_id']);
        
        if($paymentService->getPayment()->getState() === 'approved') {
        	
            //create the payment for assocciate with the book
            $localPayment = $paymentService->createLocalPayment($paymentService->getPayment());
            
            if(array_key_exists('flight_id', $input)) {
                $this->createBookingsWithFlight($input, $localPayment);
            } else {
                $this->createBookingsAndCreateFlight($input, $localPayment);
            }
            
            $bookings = AppPayment::bookingsByPayment($localPayment->getId());
            $mailService = $this->getService('Mail');
            foreach ($bookings as $booking){
                //$mailService->sendConfirmationOfAuthorizePayment($booking);    
            }
            
        } else {
            //TODO return that the payment is not approved, see what to do
        }
    }
    
    private function createBookingsWithFlight($input, $localPayment) {
        $flightId = $input['flight_id'];
        $flight = $this->getService('Flight')->find($flightId);

        $this->createPassangers($flight, $input['passengers'], $localPayment);
    }
    
    private function createBookingsAndCreateFlight($input, $localPayment) {
        $flightService = $this->getService('Flight');
        $flight = $flightService->create($input);
        
        $this->createPassangers($flight, $input['passengers'], $localPayment);
    }
    
    private function createPassangers($flight, $passangersData, $localPayment) {
        $user = $this->getCurrentUser();
        
        foreach ($passangersData as $passangerData) {
            $book = new Book();
            $book->setUser($user);
            $book->setFlight($flight);
            $book->setCompleteName($passangerData['complete_name']);
            $book->setEmail($passangerData['email']);
            $book->setCellPhone($passangerData['cell_phone']);
            $book->setAddress($passangerData['address']);
            $book->setBodyWeight($passangerData['body_weight']);
            $book->setLuggageWeight($passangerData['luggage_weight']);
            if(isset($passangerData['flexible_time']))$book->setFlexibleTime($passangerData['flexible_time']);
            
            $book->setPayment($localPayment);

            $book->save();
            
            $notify = false;
            
            if($notify){
            
    	      $origin = $flight->getOrigin->name.' ('.$flight->getOrigin->name.')';
    	      $destination = $flight->getDestination->name.' ('.$flight->getDestination->name.')';
    	      $time = $flight->departure_date.' '.($flight->departure_time==''||$flight->departure_time==null?$flight->departure_min_time.' - '.$flight->departure_max_time:$flight->departure_time);
    	                  
            //Notify
             //Push
            $data = array('time'=>$time,
                          'route'=>'from: '.$origin.' to: '.$destination,
                          'price'=>$localPayment->amount); 
                          
            $pushService = $this->getService('Push');
            $iddevice = $user->getIdFirebase();
            if(!empty($iddevice))$pushService->sendtoDeviceFCM('Notify','Flight has been requested.',$iddevice,$data);
             //Email
            } 
             
        }
    }
    
    public function testserv($input){
    	//test1
    	//$flightId = 23;
      //$flight = $this->getService('Flight')->find($flightId);
    	//return $flight->getOrigin();
    	  //return $flight->getDestination();
    	
    	//test2
    	//$id = 53;
    	//$Book = Book::find($id);
    	 //$BookF = $Book->
    	 //$BookF = $Book->getFlight();
    	//$BookF = $Book->getFlight->id;
    	//return $BookF;
    	
    	//test3
    	//$flightId = 19;
    	//$flight = $this->getService('Flight')->find($flightId);
    	  //$flight->origin;
    	  //$flight->destination;
    	//$origin = $flight->getOrigin->name.' ('.$flight->getOrigin->name.')';
    	//$destination = $flight->getDestination->name.' ('.$flight->getDestination->name.')';
    	//$time = $flight->departure_date.' '.($flight->departure_time==''||$flight->departure_time==null?$flight->departure_min_time.' - '.$flight->departure_max_time:$flight->departure_time);
    	//return array('time'=>$time,'route'=>$origin.' to '.$destination,'price'=>'');
    	
    	//test4
    	$pass = $input['b2'];
    	$result = array($input['a1'],$pass['apel'],$pass['nomb']);
    	print_r($result);
    }	
    

    /**
     * Unused functionality
     */
    public function destroy($id) {
    }

    public function find($id) {
        return Book::find($id);
    }
    
    public function findByFlight($flightId) {
        return Book::findByFlight($flightId);
    }
    
    public function findByUser() {
        $user = $this->getCurrentUser();
        return Book::findByUser($user->getId());
    }
    
    /**
     * Unused functionality
     */
    public function edit($id, $input) {
        
    }
    
    public function cancel($id) {
        $user = $this->getCurrentUser();
        
        try {
        $bookuser = $this->findByUser();
      
        //Check AppPayment::  payment_id if is not approved
        
        if(isset($bookuser[0])){
        
        if($bookuser[0]->payment_status!='completed'){  //$user->booking_status!='approved'
        
        $Booklist = Book::whereRaw('user_id = ? and flight_id = ?',array($user->id,$id));
        
        if($Booklist->count() != 0){
    		  $Bookdelete = Book::find($Booklist->first()->id);
    		  $Bookdelete->delete();
    		  
    		  $pushService = $this->getService('Push');
          $iddevice = $Booklist->first()->idOnesignal;
          if(!empty($iddevice))$pushService->sendtoDevice('Flight canceled.',$iddevice);
    		  
    		  return true;
    		}else {
          return null;
        }
        
        }else{
        	
        	return null;
        	
        }	
        
        }else{
        	return null;
        }
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return null;
        }
        
        
        
    }

}
