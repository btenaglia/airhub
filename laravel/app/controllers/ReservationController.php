<?php

/**
 * Reservation controller methods.
 *
 */
use App\Models\Book;
use App\Models\AppPayment;
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
        $paymentService = $this->getService('Payment');
        $payments = $paymentService->allWeb();
        
        if($payments !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $payments);
        } else {
            return $this->jsonResponse('No payments found.', self::HTTP_CODE_OK, []);
        }
    }

    public function destroy($id)
    {
        DB::table('books')->where('payment_id', '=', $id)->delete();
        DB::table('payments')->where('id', '=', $id)->delete();
        return $this->jsonSuccessResponse();
    }

    public function edit($id)
    {
        $reservation = Input::all();
        $payment = AppPayment::find($id);
        $payment->external_state =  $reservation['external_state'];
        $payment->update();
        return $this->jsonResponse('', self::HTTP_CODE_OK, $payment);
    }

    public function find($id)
    {
    }

    public function status(){
        $response = Input::all();
        return View::make('paypalResponse', array('content' => $response));
    }
}
