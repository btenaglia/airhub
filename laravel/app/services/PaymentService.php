<?php
namespace App\Services;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use App\Models\AppPayment;
use PayPal\Api\Authorization;
use PayPal\Api\Capture;
use PayPal\Api\Amount;

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PaymentService extends BaseService {
	  //SANDBOX
    private static $clientId = "AZo7YRikhHlZX4Oh3ry0b-gCEMVM1Ig1jWkN9XStO5fYewPLScQB64XZtbpVoFUFTrCA3rhWWbqBBLe9";
    private static $paypalSecret = "EJyGx7VNb2nMqTKlruX5aRRV-U2vyIcGkx17mT6bq-RudqtZmNRXdUsxXrjc_6EWD-mBzPSvtpu4MDPn";
    
    //PRODUCTION
    // private static $clientId = "AXtd-uLkYjDoO0-w-9i17I9pfGaJl-_KZj8KOFHyvoywPzOZ6H_quJz48kzR12jJcShMS8PVN6xMiIET";
    // private static $paypalSecret = "EAATN_QUau8ek9jsyCcfhoHzIZVC1va9LhM4GjgsoS1qtP85BUeHBXFzzmOtUfHm04QQPVt_sanRcKpH";
                                     
    const FIRST_ITEM = 0;
    
    private $apiContext;
    private $payment;
    
    public function __construct() {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                static::$clientId,
                static::$paypalSecret
            )
        );
        $this->apiContext->setConfig([
            // "mode" => "live"
            "mode" => "sandbox"
        ]); 
        set_time_limit(0);
    }
    
    public function all() {
        return AppPayment::allPayments();
    }
    
    private function getPayPalToken() {
        $credentials = $this->apiContext->getCredential();
        return $credentials->getAccessToken($this->apiContext->getConfig());
    }
    
    public function setPayment($paymentId) {
        $this->payment = Payment::get($paymentId, $this->apiContext);
    }
    
    public function sendMailPaymentAuthorized() {
        //send the mail saying to the user that the payment is approved
        //and ready to capture when the admin schedule the flight 
        $mailService = $this->getService('Mail');
        //$mailService->sendConfirmationOfAuthorizePayment($this->payment);
    }
    
    /**
     * Return the cart data object with the purchase data
     * @return type \PayPal\Api\CartData
     */
    private function getCartData() {
        $transaction = $this->payment->getTransactions();
        
        return $transaction[0];
    }

    /**
     * Create new payment in the app database
     * @param type PayPal\Api\Payment $payment the gateway payment
     */
    public function createLocalPayment() {
        $localPayment = new AppPayment();
        
        //set all the params
        $localPayment->setExternalPaymentId($this->payment->getId());
        $localPayment->setCurrency($this->getCartData()->getAmount()->getCurrency());
        $localPayment->setAmount($this->getCartData()->getAmount()->getTotal());
        $localPayment->setDescription($this->getCartData()->getDescription());
        $localPayment->setIntent($this->payment->getIntent());
        $localPayment->setExternalState($this->payment->getState());
        $localPayment->setPaymentJson($this->payment->toJSON());
        
        $localPayment->save();
        
        return $localPayment;
    }
    
    /**
     * @return type PayPal\Api\Payment
     */
    public function getPayment() {
        return $this->payment;
    }
    
    /**
     * Cature the payment, create the paypal payment from json in the database
     * @param type $localPaymentId
     */
    public function capturePayment($localPaymentId) {
        $localPayment = AppPayment::find($localPaymentId);
        
        // Get the authorization result
        $transactions = $this->getPaymentFromLocalId($localPaymentId)->getTransactions();
        $relatedResources = $transactions[0]->getRelatedResources();
        $authorization = $relatedResources[0]->getAuthorization();
        $authorizationId = $authorization->getId();
        $authorizationResult = Authorization::get($authorizationId, $this->apiContext);
        
        //Create the ammount
        $amt = new Amount();
        $amt->setCurrency($localPayment->getCurrency())
            ->setTotal($localPayment->getAmount());
        
        // Capture
        $capture = new Capture();
        $capture->setAmount($amt);
        $capture->setIsFinalCapture(true);
        
        // Perform a capture
        $theCapture = $authorizationResult->capture($capture, $this->apiContext);
        $localPayment->setCaptureJson($theCapture->toJSON());
        $localPayment->setCaptureState($theCapture->getState());
        $success = $localPayment->update();
        
        /*       
        $jsonCapture = Capture::get($theCapture->getId(), $this->apiContext)->toJSON();
        $localPayment->getCaptureJson($jsonCapture);
        $localPayment->setCaptureState($theCapture->getState());
        */
        
        
        
        
        if($success){
            
            $bookings = AppPayment::bookingsByPayment($localPaymentId);
            //$mailService = $this->getService('Mail');
            $pushService = $this->getService('Push');

            //foreach ($bookings as $booking){
                //$mailService->sendApprovedBooking($booking);    
            //}
            $iddevice = $bookings[0]->idOnesignal;
            //$pushService->sendtoAll('Flight confirmed.');
            if(!empty($iddevice))$pushService->sendtoDevice('Flight confirmed.',$iddevice);
        } 
        
        return $success;
    }
    
    private function getPaymentFromLocalId($localPaymentId) {
        $localPayment = AppPayment::find($localPaymentId);
        $payment = new Payment();
        $payment->fromJson($localPayment->getPaymentJson());
        
        return $payment;
    }
}
