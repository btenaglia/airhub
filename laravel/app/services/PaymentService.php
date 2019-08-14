<?php
namespace App\Services;

use App\Models\AppPayment;
use Braintree\Gateway;
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
//use Berkayk\OneSignal\OneSignalServiceProvider as  OneSignal;
use URL;

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PaymentService extends BaseService
{
    //SANDBOX
    // private static $clientId = "AViTmsSgc7pfq0rbQxKsBu10Jad9ozAXBZsrz0Fx26QYiiEB9TnGw-gAiYIxyQPfDXUcnn_aOSq3DGqT";
    // private static $paypalSecret = "EDuLdJ_wKjKTZafOows_skhvlUbn0W2iAdBdRkCFt0fqqh-MBBn-kcJ9ojLkEhGaCCufQZdlql7yHYAg";

    //PRODUCTION
    private static $clientId = "AaluMuqhBUiC4-MjYFd_5si7VhYyCeaWZIJsVGB9QNCtfK8ggVCM113pGu-ICnOQgfbYC7vLQZXdlRgP";
    private static $paypalSecret = "EEZvT9K5rF2voaC7RzIjQ6xsLCeqH212yIL52HOz9N8k7dG5nbBCCDdsfbVPPs0V58E3OKroRQwhKL7s";

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
            "mode" => "live",
            //"mode" => "sandbox",
        ]);

    }
    public function prueba()
    {
        // return base_path() . '/vendor';
        $gateway = new Gateway([
            'environment' => 'sandbox',
            'merchantId' => '3x28yn7jxvp6cm56',
            'publicKey' => '5cgvp4bd8t2t2vx4',
            'privateKey' => '8e0cfa9e3a90bf8b19d3858b0b720566',
        ]);
        $clientToken = $gateway->clientToken()->generate();
        return $clientToken;
    }

    public function paymentPayaForm($info)
    {
        $user_hash_key = '11e9baac440aa3c09f871199'; // secret hash key used for hashing the variables
        $user_id = '11e9b2f3153f37a6b9c52525'; //  variables for generating the required hash
        $timestamp = time(); // variables for generating the required hash
        $salt = $user_id . $timestamp; //$user_id and $timestamp need to be in this order
        $developer_id = "u41Si9JY";
        // $terminal_id = "11e9b3dea73c5fbc9739af9b";
        $location_id = "11e9b2f3143c63babaf3548c";
        $transaction_api_id = "11e9b2f3145792caaff2097f"; // generarlo dinamicamente CREO
        $domain = "https://api.sandbox.payaconnect.com";
        $endpoint = "accountform";
        // $transaction = '{
        //     "transaction":{
        //     "payment_method": "cc",
        //     "transaction_amount": "' . $info['price'] . '",
        //     "action": "sale",
        //     "location_id": "' . $location_id . '",
        //     "terminal_id": "' . $terminal_id . '",
        //     "transaction_api_id":"' . $transaction_api_id . '"
        //     }
        // }';
        // Required fields

        $accountvault = '{
            "accountvault":{
            "payment_method": "cc",
            "location_id": "11e9b2f3143c63babaf3548c",
            "account_vault_api_id": "123",
            "title": "Account_vault",
            "account_holder_name": "john smith",
            "show_account_holder_name": true,
            "show_street": true,
            "show_zip": true,
            "stylesheet_url": "{full URL - i.e. https://third.party.domain/css/styles.css}",
            "display_close_button": true,
            "parent_close": true,
            "parent_close_delay": 3,
            "parent_origin": null,
            "redirect_url_on_approval": "https://www.google.com",
            "redirect_url_delay": 10
            }
        }';

        $data = implode(unpack("H*", $accountvault));
        $hash_key = hash_hmac('sha256', $salt, $user_hash_key);

        return $url = sprintf("%s/v2/%s?developer-id=%s&hash-key=%s&user-id=%s&timestamp=%s&data=%s",
            $domain,
            $endpoint,
            $developer_id,
            $hash_key,
            $user_id,
            $timestamp,
            $data
        );
    }
    public function all()
    {
        return AppPayment::allPayments();
    }
    public function allWeb()
    {
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
