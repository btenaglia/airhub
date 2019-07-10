<?php
namespace App\Services;

use App\Models\AppPayment;
use PayPal\Api\Amount;
use PayPal\Api\Authorization;
use PayPal\Api\Capture;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use URL;

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PaymentService extends BaseService
{
    //SANDBOX
    private static $clientId = "AViTmsSgc7pfq0rbQxKsBu10Jad9ozAXBZsrz0Fx26QYiiEB9TnGw-gAiYIxyQPfDXUcnn_aOSq3DGqT";
    private static $paypalSecret = "EDuLdJ_wKjKTZafOows_skhvlUbn0W2iAdBdRkCFt0fqqh-MBBn-kcJ9ojLkEhGaCCufQZdlql7yHYAg";

    //PRODUCTION
    // private static $clientId = "AViTmsSgc7pfq0rbQxKsBu10Jad9ozAXBZsrz0Fx26QYiiEB9TnGw-gAiYIxyQPfDXUcnn_aOSq3DGqT";
    // private static $paypalSecret = "EDuLdJ_wKjKTZafOows_skhvlUbn0W2iAdBdRkCFt0fqqh-MBBn-kcJ9ojLkEhGaCCufQZdlql7yHYAg";

    const FIRST_ITEM = 0;

    private $apiContext;
    private $payment;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                static::$clientId,
                static::$paypalSecret
            )
        );
        $this->apiContext->setConfig([
            //"mode" => "live",
            "mode" => "sandbox",
        ]);

    }

    public function all()
    {
        return AppPayment::allPayments();
    }
    public function allWeb(){
        return AppPayment::allPaymentsWeb();
    }
    public function getPayPalToken()
    {
        $credentials = $this->apiContext->getCredential();
        return $credentials->getAccessToken($this->apiContext->getConfig());
    }

    public function setPayment($paymentId)
    {
        $this->payment = Payment::get($paymentId, $this->apiContext);
    }

    public function sendMailPaymentAuthorized()
    {
        //send the mail saying to the user that the payment is approved
        //and ready to capture when the admin schedule the flight
        $mailService = $this->getService('Mail');
        //$mailService->sendConfirmationOfAuthorizePayment($this->payment);
    }

    /**
     * Return the cart data object with the purchase data
     * @return type \PayPal\Api\CartData
     */
    public function getCartData()
    {
        $transaction = $this->payment->getTransactions();

        return $transaction[0];
    }

    /**
     * Create new payment in the app database
     * @param type PayPal\Api\Payment $payment the gateway payment
     */
    public function createLocalPayment()
    {
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
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Cature the payment, create the paypal payment from json in the database
     * @param type $localPaymentId
     */
    public function capturePayment($localPaymentId)
    {
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

        if ($success) {

            $bookings = AppPayment::bookingsByPayment($localPaymentId);
            //$mailService = $this->getService('Mail');
            $pushService = $this->getService('Push');

            //foreach ($bookings as $booking){
            //$mailService->sendApprovedBooking($booking);
            //}
            $iddevice = $bookings[0]->idOnesignal;
            //$pushService->sendtoAll('Flight confirmed.');
            if (!empty($iddevice)) {
                $pushService->sendtoDevice('Flight confirmed.', $iddevice);
            }

        }

        return $success;
    }

    private function getPaymentFromLocalId($localPaymentId)
    {
        $localPayment = AppPayment::find($localPaymentId);
        $payment = new Payment();
        $payment->fromJson($localPayment->getPaymentJson());

        return $payment;
    }

    public function payWithpaypal($reservation)
    {
        $price = $reservation['price'] * $reservation['seats'];
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName('passages') /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($price); /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($price);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Airplane passages (x' . $reservation['seats'] . ')');
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(url("web/reservation/status")) /** Specify return URL **/
            ->setCancelUrl(url("web/reservation/status"));
        $payment = new Payment();
        $payment->setIntent('authorize')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        //   dd($payment->create($this->apiContext)->toArray());exit;
        try {
            return $payment->create($this->apiContext)->toArray();
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
                \Session::put('error', 'Connection timeout');
                return Redirect::route('paywithpaypal');
            } else {
                \Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::route('paywithpaypal');
            }
        }

    }

}
