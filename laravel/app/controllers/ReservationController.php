<?php

/**
 * Reservation controller methods.
 *
 */
class ReservationController extends BaseController implements GenericControllers
{

    public function create()
    {
        $newReservation = Input::all();
        
        return $this->jsonResponse('', self::HTTP_CODE_OK, $newReservation);
    }

    public function all() {
    }

    

    public function destroy($id) {
    }

    public function edit($id) {
    }

    public function find($id) {
    }
}
