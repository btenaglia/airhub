<?php

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class BookController extends BaseController implements GenericControllers {
    
    public function all() {
        $bookingService = $this->getService('Book');
        $bookings = $bookingService->all();
        
        if($bookings !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $bookings);
        } else {
            return $this->jsonResponse('No flights found.', self::HTTP_CODE_OK, []);
        }
    }

    public function create() {
        $bookingService = $this->getService('Book');
        
        $bookingService->create(Input::all());
        
        return $this->jsonSuccessResponse();
    }
    
    public function testcc() {
        $bookingService = $this->getService('Book');
        
        $result = $bookingService->testserv(Input::all());
        
        //print_r( $result);
    }
    
    /**
     * Unused for this model
     */
    public function destroy($id) {

    }
    
    /**
     * Unused for this model
     */
    public function edit($id) {

    }

    public function find($id) {
        $bookingService = $this->getService('Book');        
        $book = $bookingService->find($id);
        
        if($book !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $book);
        } else {
            return $this->jsonResponse('Not book found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }
    
    public function findByFlight($id) {
        $bookingService = $this->getService('Book');
        $bookings = $bookingService->findByFlight($id);
        
        if($bookings !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $bookings);
        } else {
            return $this->jsonResponse('No flights found.', self::HTTP_CODE_OK, []);
        }
    }
    
    public function findByUser() {

        $bookingService = $this->getService('Book');
        $bookings = $bookingService->findByUser();
     
        if($bookings !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $bookings);
        } else {
            return $this->jsonResponse('No flights found.', self::HTTP_CODE_OK, []);
        }
    }
    
    public function cancel($id) {
        $bookingService = $this->getService('Book');
        
        $bookings = $bookingService->cancel($id);
        
        if($bookings !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $bookings);
        } else {
            return $this->jsonResponse('Not booking found or can not be deleted.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

}
