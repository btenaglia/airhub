<?php

class InfoController extends BaseController
{

    public function sendMailRequestCharter()
    {
        $input = Input::all();
        $srvPlace = $this->getService('Place');
        if ($input['origin'] == 0) {
            $input['origin'] = $input['customOrigin'];
        } else {
            $input['origin'] = $srvPlace->find($input['origin'])->name;
        }

        if ($input['destination'] == 0) {
            $input['destination'] = $input['customDestination'];
        } else {
            $input['destination'] = $srvPlace->find($input['destination'])->name;
        }
        
        $srv = $this->getService('Mail');
        $srv->infoCharter($input);
        return $this->jsonResponse('', self::HTTP_CODE_OK, $input);
    }
    public function ContactEmail()
    {
        $input = Input::all();
        $srv = $this->getService('Mail');
        $srv->sendContact($input);
        return $this->jsonResponse('', self::HTTP_CODE_OK, $input);
    }
    public function sendPdf(){
        $input = Input::all();
        $srv = $this->getService('Mail');
        $srvData = $srv->sendPdf($input);
        $data2 = ["data" => $srvData];
        return $this->jsonResponse('', self::HTTP_CODE_OK,  $data2);
    }
    public function getPdf(){
        $info = Input::all();
        $data = [
            "user_id"=> "80",
            "origin_id"=> "2",
            "destination_id"=> "3",
            "seats"=> "2",
            "flight_id"=> 142,
            "body_weight"=> 215,
            "complete_name"=> "Campese, Bob",
            "luggage_weight"=> 16,
            "extras"=> [
              
                "complete_name"=> "Juan test",
                "body_weight"=> 123,
                "luggage_weight"=> 44,
                "email"=> "qqqq@rrrr.com",
                "address"=>"irene curie, 139",
                "cell_phone"=> "654654654"
              
            ],
            "seats_limit"=> 7,
            "weight_limit"=> 1000,
            "price"=> 617
        ];
        return View::make('pdfs/templateInfo', array('data' => $data));
    }

    public function textoMail(){

        return View::make('mails/pdf');
    }
}
