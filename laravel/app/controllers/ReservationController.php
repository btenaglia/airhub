<?php

/**
 * Reservation controller methods.
 *
 */
use App\Models\Book;
use Illuminate\Http\Request;
class ReservationController extends BaseController implements GenericControllers
{

    public function create()
    {
        $newReservation = Input::all();
        $reservationService = $this->getService('Book');
        $reservation = $reservationService->ReservationValidation($newReservation);
        if($reservation == 'capacity')
        return $this->jsonResponse('Reservation could not create, exceeded  capacity', self::HTTP_CODE_SERVER_ERROR, []);
        if($reservation == 'weight')
        return $this->jsonResponse('Reservation could not create, exceeded  weight', self::HTTP_CODE_SERVER_ERROR, []);
      
        return $this->jsonResponse('', self::HTTP_CODE_OK, $reservation);
    }

    public function all()
    {
    }

    public function destroy($id)
    {
    }

    public function edit($id)
    {
    }

    public function find($id)
    {
    }

    public function status(){
        $response = Input::all();
        return View::make('paypalResponse', array('content' => $response));
    }
}
