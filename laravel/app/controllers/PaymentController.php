<?php
use Illuminate\Support\Facades\Storage;
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
    public function paymentPaya()
    {
        $paymentsrv = $this->getService('Payment');
        $url = $paymentsrv->paymentWithPaya(Input::all());
        
            // $curl = curl_init();
            // $url = 'https://api.sandbox.payaconnect.com/v2/transactions';
            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => $url,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 30,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "POST",
            //     CURLOPT_POSTFIELDS => "{\n
            //     \"transaction\": {\n
            //             \"transaction_amount\": \"10\",\n
            //             \"location_id\": \"11e9b2f3143c63babaf3548c\",\n
            //             \"action\": \"sale\"\n
            //     }\n}",
            //     CURLOPT_HTTPHEADER => array(
            //         "content-type: application/json",
            //         "developer-id: u41Si9JY",
            //         "user-api-key: 11e9baac440aa3c09f871199",
            //         "user-id: 11e9b2f3153f37a6b9c52525"
            //     ),
            // ));

            // $response = curl_exec($curl);
            // $err = curl_error($curl);

            // curl_close($curl);

            // if ($err) {
            //     echo "cURL Error #:" . $err;
            // } else {
            //     echo $response;
            // }  
        return $this->jsonResponse('', self::HTTP_CODE_OK, $url["url"]);
        
    }
    public function responseVauls(){

        $data = implode(Input::all());
        // $dataa = $request;
        File::put('mytextdocument.txt',$data);
        // File::put('mytextdocument.txt',$dataa);
        return View::make('paypalResponse', array('content' => $data));
            // Storage::put('response.txt', "hola");

            // Storage::put('file.jpg', $resource);
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
