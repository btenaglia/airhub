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
}
