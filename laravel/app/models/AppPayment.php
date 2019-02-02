<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class AppPayment extends BaseModel {
    protected $table = 'payments';
    
    public function getId() {
        return $this->id;
    }
    
    public function getExternalPaymentId() {
        return $this->external_payment_id;
    }

    public function setExternalPaymentId($externalPaymentId) {
        $this->external_payment_id = $externalPaymentId;
    }
    
    public function getExternalState() {
        return $this->external_state;
    }

    public function setExternalState($externalState) {
        $this->external_state = $externalState;
    }
    
    public function getCaptureState() {
        return $this->capture_state;
    }

    public function setCaptureState($captureState) {
        $this->capture_state = $captureState;
    }
    
    public function getCurrency() {
        return $this->currency;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    
    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }
    
    public function getIntent() {
        return $this->intent;
    }

    public function setIntent($intent) {
        $this->intent = $intent;
    }
    
    public function getPaymentJson() {
        return $this->payment_json;
    }

    public function setPaymentJson($payment_json) {
        $this->payment_json = $payment_json;
    }
    
    public function getCaptureJson() {
        return $this->capture_json;
    }

    public function setCaptureJson($capture_json) {
        $this->capture_json = $capture_json;
    }

    public function getBooks() {
        return $this->hasMany('App\Models\Book');
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    /*public static function allPayments() {
        return DB::table('payments')
                ->select(DB::raw(self::rawForSelect()))
                ->get();
    }*/
    
    public static function allPayments() {
        return DB::table('payments')
                ->join('books', 'books.payment_id', '=', 'payments.id')
                ->select(DB::raw(self::rawForBookingsPayment()))
                ->orderBy('payments.created_at', 'desc')
                ->get();
    }
    
    public static function bookingsByPayment($id) {
        return DB::table('payments')
                ->join('books', 'books.payment_id', '=', 'payments.id')
                ->join('users', 'users.id', '=', 'books.user_id')  
                ->where('payments.id','=', $id)
                ->select(DB::raw(self::rawForBookingsByPayment()))
                ->get();
    }
    
    private static function rawForSelect(){
        return "
            payments.external_payment_id,
            payments.currency,
            payments.amount,
            payments.description,
            payments.intent,
            payments.external_state,
            payments.created_at";
    }
    
    private static function rawForBookingsByPayment(){
        return "
            books.id AS books_id,
            books.complete_name AS passangerName,
            users.id AS user_id,
            users.complete_name AS userName,
            users.email AS userEmail,
            users.id_onesignal AS idOnesignal";
    }
    
    private static function rawForBookingsPayment(){
        return "
            books.id AS books_id,
            books.complete_name,
            books.email,
            @var2:=mid(payment_json,@var1:=locate('payment_method',payment_json)+17,locate(',',payment_json,@var1)-@var1-1) as payment_method,
            IF(@var2 = 'credit_card', concat(mid(payment_json,@var3:=locate('type',payment_json)+7,locate(',',payment_json,@var3)-@var3-1),'-',if((@var4:=locate('number',payment_json)+9)>9,mid(payment_json,@var4,locate(',',payment_json,@var4)-@var4-1),concat('xxxxxxxxxxxx',mid(payment_json,@var6:=locate('last4',payment_json)+8,locate(',',payment_json,@var6)-@var6-1)))), mid(payment_json,@var5:=locate('payer_id',payment_json)+11,locate(',',payment_json,@var5)-@var5-1)) as payment_id,
            payments.external_payment_id,
            payments.currency,
            payments.amount,
            payments.description,
            payments.payment_json,
            payments.capture_state,
            payments.intent,
            payments.external_state,
            payments.created_at";
    }
    
}
