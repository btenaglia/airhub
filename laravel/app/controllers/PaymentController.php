<?php

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PaymentController extends BaseController
{

 
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
    public function testBrain(){
        $bookingService = $this->getService('Payment');
        
        $asd  = $bookingService->prueba();

        return $this->jsonResponse('', self::HTTP_CODE_OK, $asd);
        // return $this->jsonSuccessResponse();
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
