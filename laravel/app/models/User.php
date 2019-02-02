<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends BaseModel implements UserInterface, RemindableInterface {
    protected $table = 'users';
    use UserTrait,
        RemindableTrait;

    const USER_TYPE_ADMIN = 'admin';
    const USER_TYPE_APP = 'app_user';
    
    public function getId() {
        return $this->id;
    }

    public function getCompleteName() {
        return $this->complete_name;
    }

    public function setCompleteName($completeName) {
        $this->complete_name = $completeName;
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($Name) {
        $this->name = $Name;
    }
    
    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($LastName) {
        $this->last_name = $LastName;
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function setAddress($address) {
        $this->address = $address;
    }
    
    public function getCellPhone() {
        return $this->cell_phone;
    }
    
    public function setCellPhone($cellPhone) {
        $this->cell_phone = $cellPhone;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }

    public function getBooks() {
        return $this->hasMany('App\Models\Book');
    }
    
    public function getFlights() {
        return $this->hasMany('App\Models\Flight');
    }
    
    public function getBookByFlight($userId,$flightId) {
    	  //->select(DB::raw(self::rawForSelectByFlight()))
        return DB::table('books')
                ->join('payments', 'payments.id', '=', 'books.payment_id')
                ->join('flights', 'flights.id', '=', 'books.flight_id')
                ->join('places as origin', 'origin.id', '=', 'flights.origin')
                ->join('places as destination', 'destination.id', '=', 'flights.destination')
                ->where('books.user_id','=', $userId)
                ->where('books.flight_id','=', $flightId)
                ->where('flights.active','=', 1)
                ->select(DB::raw(self::rawForSelectByUser()))
                ->get();
    }
    
    public function getFacebookik() {
        return $this->facebookid;
    }
    
    public function setFacebookik($facebookid) {
        $this->facebookid = $facebookid;
    }
    
    public function getFacebooktoken() {
        return $this->facebooktoken;
    }
    
    public function setFacebooktoken($facebooktoken) {
        $this->facebooktoken = $facebooktoken;
    }
    
    public function getIdOnesignal() {
        return $this->id_onesignal;
    }
    
    public function setIdOnesignal($idonesignal) {
        $this->id_onesignal = $idonesignal;
    }
    
    public function getIdFirebase() {
        return $this->fcm_token_device;
    }
    
    public function setIdFirebase($idfirebase) {
        $this->fcm_token_device = $idfirebase;
    }
    
    public function getPromoCode() {
        return $this->promo_code;
    }
    
    public function setPromoCode($promocode) {
        $this->promo_code = $promocode;
    }
    
        public function getCity() {
        return $this->city;
    }
    
    public function setCity($city) {
        $this->city = $city;
    }
    
    public function getState() {
        return $this->state;
    }
    
    public function setState($state) {
        $this->state = $state;
    }    
    
    public function getCountry() {
        return $this->country;
    }
    
    public function setCountry($country) {
        $this->country = $country;
    }
    
    public function getZipcode() {
        return $this->zipcode;
    }
    
    public function setZipcode($zipcode) {
        $this->zipcode = $zipcode;
    }    
    
    public function getCreateMode() {
        return $this->create_mode;
    }
    
    public function setCreateMode($create_mode) {
        $this->create_mode = $create_mode;
    }

    public function getUserType() {
        return $this->user_type;
    }
    
    public function setUserType($userType) {
        if(!in_array($userType, $this->getAllowedUserTypes())) {
            //TODO Throw an exception
        }
        $this->user_type = $userType;
    }
    
    public function getAllowedUserTypes() {
        return [
            self::USER_TYPE_ADMIN,
            self::USER_TYPE_APP,
        ];
    }
    
    public function getBodyWeight() {
        return $this->body_weight;
    }
    
    public function setBodyWeight($bodyWeight) {
        return $this->body_weight = $bodyWeight;
    }
    
    public static function findByEmail($email) {
        return User::where('email', '=', $email)->firstOrFail();
    }
    
    public static function findByCellphone($cellphone) {
        return User::where('cell_phone', '=', $cellphone)->firstOrFail();
    }
    
    public static function findAppUserByIdFB($id) {
        return User::where('facebookid', '=', $id)->firstOrFail();
    }
    
    public static function findAppUserById($id) {
        return User::where('id', '=', $id)->firstOrFail();
    }
    
    public static function findByCredentials($credentials) {
        $user = User::findByEmail($credentials["email"]);
        $decyptedPassword = \Crypt::decrypt($user->getPassword());
        
        if($credentials['password'] === $decyptedPassword) {
            return $user;
        } else {
            return null;
        }
    }
    
    public static function findAppUserByCredentials($credentials) {
        
        if (filter_var($credentials["email"], FILTER_VALIDATE_EMAIL))
           $user = User::findByEmail($credentials["email"]);
        else
           $user = User::findByCellphone($credentials["email"]);
           
        //var_dump($user);   
           
        $decyptedPassword = \Crypt::decrypt($user->getPassword());
        
        if($credentials['password'] === $decyptedPassword && $user->getUserType() === self::USER_TYPE_APP) {
            return $user;
        } else {
            return null;
        }
        	
    }
    
    private static function rawForSelectByUser() {
        return 
            "
                flights.id,
                flights.departure_date,
                DATE_FORMAT(flights.departure_time, '%H:%i') AS departure_time,
                DATE_FORMAT(flights.departure_min_time, '%H:%i') AS departure_min_time,
                DATE_FORMAT(flights.departure_max_time, '%H:%i') AS departure_max_time,
                origin.name as origin_name,
                destination.name as destination_name,
                payments.amount AS amount,
                flights.status AS flight_status,
                payments.external_state AS booking_status,
                payments.capture_state AS payment_status
                "
        ;
    }
    
    private static function rawForSelectByFlight() {
        return 
            "
                books.*,
                payments.*,
                users.complete_name AS user_name"
        ;
    }
}
