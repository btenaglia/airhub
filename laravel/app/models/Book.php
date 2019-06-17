<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Book extends BaseModel {
    protected $table = 'books';
    protected $fillable = ['flight_id','user_id','payment_id'];
    public function getId() {
        return $this->id;
    }
    
    public function getCompletName() {
        return $this->complete_name;
    }
    
    public function setCompleteName($completeName) {
        $this->complete_name = $completeName;
    }
    
    public function getBodyWeight() {
        return $this->body_weight;
    }
    
    public function setBodyWeight($bodyWeight) {
        $this->body_weight = $bodyWeight;
    }
    
    public function getLuggageWeight() {
        return $this->luggage_weight;
    }
    
    public function setLuggageWeight($luggageWeight) {
        $this->luggage_weight = $luggageWeight;
    }
    
    public function getFlight() {
        return $this->belongsTo('App\Models\Flight', 'flight_id');
    }
    
    public function setFlight($flight) {
        $this->getFlight()->associate($flight);
    }
    
    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
    public function setUser($user) {
        $this->getUser()->associate($user);
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
    
    public static function allWithFlights() {
        return DB::table('books')
                ->join('payments', 'payments.id', '=', 'books.payment_id')
                ->join('flights', 'flights.id', '=', 'books.flight_id')
                ->select(DB::raw(self::rawForSelect()))
                ->get();
    }
    
    public static function findByFlight($flightId) {
        return DB::table('books')
                ->join('payments', 'payments.id', '=', 'books.payment_id')
                ->join('users', 'users.id', '=', 'books.user_id')
                ->join('flights', 'flights.id', '=', 'books.flight_id')
                ->where('books.flight_id','=', $flightId)
                ->select(DB::raw(self::rawForSelectByFlight()))
                ->get();
    }
    
    public static function findByUser($userId) {
        return DB::table('books')
                ->join('payments', 'payments.id', '=', 'books.payment_id')
                ->join('flights', 'flights.id', '=', 'books.flight_id')
                ->join('places as origin', 'origin.id', '=', 'flights.origin')
                ->join('places as destination', 'destination.id', '=', 'flights.destination')
                ->where('books.user_id','=', $userId)
                ->where('flights.active','=', 1)
                ->groupBy('payments.id')
                ->orderBy('departure_date', 'asc')
                ->orderBy('departure_min_time', 'asc')    
                ->orderBy('departure_time', 'asc')
                ->select(DB::raw(self::rawForSelectByUser()))
                ->get();
    }
    
    private static function rawForSelect() {
        return 
            "
                books.*,
                payments.*"
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
    
    private static function rawForSelectByUser() {
        return 
            "
                flights.id AS flightID,
                flights.departure_date,
                DATE_FORMAT(flights.departure_time, '%H:%i') AS departure_time,
                DATE_FORMAT(flights.departure_min_time, '%H:%i') AS departure_min_time,
                DATE_FORMAT(flights.departure_max_time, '%H:%i') AS departure_max_time,
                origin.name as origin_name,
                destination.name as destination_name,
                COUNT(books.id) AS total_seats,
                payments.currency AS currency,
                SUM(payments.amount)/COUNT(books.id) AS total_cost,
                flights.status AS flight_status,
                payments.external_state AS booking_status,
                payments.capture_state AS payment_status
                "
        ;
    }
    
    public function getPayment() {
        return $this->belongsTo('App\Models\AppPayment', 'payment_id');
    }
    
    public function setPayment($payment) {
        $this->getPayment()->associate($payment);
    }
}
