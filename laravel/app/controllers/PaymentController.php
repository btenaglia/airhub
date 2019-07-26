<?php

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PaymentController extends BaseController
{
    const HTTP_CODE_CONFLICT = 409;
    public function all()
    {
        $paymentService = $this->getService('Payment');
        $payments = $paymentService->all();

        if ($payments !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $payments);
        } else {
            return $this->jsonResponse('No payments found.', self::HTTP_CODE_OK, []);
        }
    }
    public function paymentPaya()
    {
        $paymentsrv = $this->getService('Payment');
        $url = $paymentsrv->paymentWithPaya(Input::all());

        return $this->jsonResponse('', self::HTTP_CODE_OK, $url["url"]);

    }
    public function reservationMobileCreate()
    {
        $paymentsrv = $this->getService('Book');
        $url = $paymentsrv->ReservationMobile(Input::all());
        
        if ($url == 'capacity') {
            return $this->jsonResponse('Capacity Exceed', self::HTTP_CODE_CONFLICT, []);
        } else if ($url == 'weight') {
            return $this->jsonResponse('Weight Exceed', self::HTTP_CODE_CONFLICT, []);
        } else {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $url);
        }
        
    }
    public function showIframePaya()
    {
        $data = Input::all();
        $paymentsrv = $this->getService('Payment');
        $url = $paymentsrv->payments($data);
        return View::make('iframePaya', array('content' => $data));
    }
    public function responseVauls()
    {
        $data = implode(Input::all());
        File::put('mytextdocument.txt', $data);
        return View::make('paypalResponse', array('content' => $data));

    }
    public function getToken(){
        
        $bookingService = $this->getService('Payment');
        $token = Array("token" => $bookingService->getTokenBrainTree());
        return $this->jsonResponse('', self::HTTP_CODE_OK, $token);
    }
    public function capturePayment($id)
    {
        $paymentService = $this->getService('Payment');
        $success = $paymentService->capturePayment($id);

        if ($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Cannot capture the payment', self::HTTP_CODE_OK, []);
        }
    }

    public function getTicketCost()
    {
        $configService = $this->getService('Mconfig');
        $cost = $configService->getValueMconfig('paramcode', 'ticket_cost', 'paramvalueamount');
        $costff = $cost[0]->paramvalueamount;
        return $this->jsonResponse('', self::HTTP_CODE_OK, ["ticket_cost" => $costff]);
    }

    public function getTicketCost2()
    {
        //$seat = Input::get('n_seat');
        /*$configService = $this->getService('Mconfig');
        $cost = $configService->getValueMconfig('paramcode','ticket_cost','paramvalueamount');
        $costff = $cost[0]->paramvalueamount;
        return $this->jsonResponse('', self::HTTP_CODE_OK, ["ticket_cost" => $costff]);*/
        $input = Input::all();

        // $profileService = $this->getService('Profile');
        if (array_key_exists('flight_id', $input)) {
            $flightid = $input;
            $price = $this->getService('Flight')->find($flightid);
            $price = $price[0]["price"];

        } else {
            $price = false;
        }

        return $this->jsonResponse('', self::HTTP_CODE_OK, ["ticket_cost" => $price]);
        // if(!($price===false)){
        //  return $this->jsonResponse('', self::HTTP_CODE_OK, ["ticket_cost" => $price]);
        // }else{
        //  $configService = $this->getService('Mconfig');
        //    $cost = $configService->getValueMconfig('paramcode','ticket_cost','paramvalueamount');
        //    $costff = $cost[0]->paramvalueamount;
        //  return $this->jsonResponse('', self::HTTP_CODE_OK, ["ticket_cost" => $costff]);
        // }
    }

}
