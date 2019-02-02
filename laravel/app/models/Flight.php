<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Flight extends BaseModel {
    protected $table = 'flights';
    
    const STATUS_PROPOSED = 'proposed';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_TIME = 'in_time';
    const STATUS_LANDED = 'landed';
    
    public function getId() {
        return $this->id;
    }
    
    public function getOrigin() {
        return $this->belongsTo('App\Models\Place', 'origin');
    }
    
    public function setOrigin($origin) {
        $this->getOrigin()->associate($origin);
    }
    
    public function getDestination() {
        return $this->belongsTo('App\Models\Place', 'destination');
    }
    
    public function setDestination($destination) {
        $this->getDestination()->associate($destination);
    }
    
    public function getDepartureTime() {
        return $this->departure_time;
    }
    
    public function setDepartureTime($departureTime) {
        $this->departure_time = $departureTime;
    }
    
    public function getDepartureDate() {
        return $this->departure_date;
    }
    
    public function setDepartureDate($departureDate) {
        $this->departure_date = $departureDate;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        if(!in_array($status, self::getAllowedStatus())) {
            //TODO Throw an exception
        }
        $this->status = $status;
    }
    
    public function getAmount() {
        return $this->price;
    }
    
    public function setAmount($amount) {
        $this->price = $amount;
    }
    
    public static function getAllowedStatus() {
        return [
            self::STATUS_IN_TIME,
            self::STATUS_LANDED,
            self::STATUS_PROPOSED,
            self::STATUS_SCHEDULED
        ];
    }
    
    public static function getCreatedStatus() {
        return [
            self::STATUS_PROPOSED
        ];
    }
    
    public function getDepartureMinTime() {
        return $this->departure_min_time;
    }
    
    public function setDepartureMinTime($departureMinTime) {
        $this->departure_min_time = $departureMinTime;
    }
    
    public function getDepartureMaxTime() {
        return $this->departure_max_time;
    }
    
    public function setDepartureMaxTime($departureMaxTime) {
        $this->departure_max_time = $departureMaxTime;
    }
    
    public function getPlane() {
        return $this->belongsTo('App\Models\Plane', 'plane_id');
    }
    
    public function setPlane($plane) {
        $this->getPlane()->associate($plane);
    }
    
    public function getBooks() {
        return $this->hasMany('App\Models\Book');
    }
    
    public function getUser() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
    
    public function setUser($user) {
        $this->getUser()->associate($user);
    }
    
    public static function allWithRelationsAttrs() {
        return DB::table('flights')
                ->join('places as origin', 'origin.id', '=', 'flights.origin')
                ->join('places as destination', 'destination.id', '=', 'flights.destination')
                ->leftJoin('planes', 'planes.id', '=', 'flights.plane_id')
                ->leftJoin('books', 'books.flight_id', '=', 'flights.id')
                ->join('users', 'users.id', '=', 'flights.created_by')  
                ->where('flights.active','=', 1)
                ->groupBy('flights.id')
                ->orderBy('departure_date', 'asc')
                ->orderBy('departure_min_time', 'asc')    
                ->orderBy('departure_time', 'asc')
                ->select(DB::raw(self::rawForSelectOrig()))
                ->get();
    }
    
    public static function futureFlights() {
        return DB::table('flights')
                ->join('places as origin', 'origin.id', '=', 'flights.origin')
                ->join('places as destination', 'destination.id', '=', 'flights.destination')
                ->leftJoin('planes', 'planes.id', '=', 'flights.plane_id')
                ->leftJoin('books', 'books.flight_id', '=', 'flights.id')
                ->join('users', 'users.id', '=', 'flights.created_by')  
                ->where('flights.active','=', 1)
                ->where('flights.departure_date','>=', date('Y-m-d'))
                ->groupBy('flights.id')
                ->orderBy('departure_date', 'asc')
                ->orderBy('departure_min_time', 'asc')    
                ->orderBy('departure_time', 'asc')
                ->select(DB::raw(self::rawForSelect()))
                ->get();
    }
    
    public static function passedFlights() {
        return DB::table('flights')
                ->join('places as origin', 'origin.id', '=', 'flights.origin')
                ->join('places as destination', 'destination.id', '=', 'flights.destination')
                ->leftJoin('planes', 'planes.id', '=', 'flights.plane_id')
                ->leftJoin('books', 'books.flight_id', '=', 'flights.id')
                ->join('users', 'users.id', '=', 'flights.created_by')  
                ->where('flights.active','=', 1)
                ->where('flights.departure_date','<', date('Y-m-d'))
                ->groupBy('flights.id')
                ->orderBy('departure_date', 'asc')
                ->orderBy('departure_min_time', 'asc')    
                ->orderBy('departure_time', 'asc')
                ->select(DB::raw(self::rawForSelect()))
                ->get();
    }
    
    public static function userByFlight($id) {
        return DB::table('flights')
                ->leftJoin('books', 'books.flight_id', '=', 'flights.id')
                ->join('users', 'users.id', '=', 'books.user_id')  
                ->where('flights.id','=', $id)
                ->select(DB::raw(self::rawForUserBYFlight()))
                ->get();
    }
    
    private static function rawForUserBYFlight(){
        return "
            users.*";
    }
    
    public static function findWithRelationsAttrs($id) {
        return DB::table('flights')
                ->join('places as origin', 'origin.id', '=', 'flights.origin')
                ->join('places as destination', 'destination.id', '=', 'flights.destination')
                ->leftJoin('planes', 'planes.id', '=', 'flights.plane_id')
                ->leftJoin('books', 'books.flight_id', '=', 'flights.id')
                ->join('users', 'users.id', '=', 'flights.created_by')
                ->where('flights.active','=', 1)
                ->where('flights.id', '=', $id)
                ->select(DB::raw(self::rawForSelect()))
                ->first();
    }
    
    private static function rawForSelectOrig(){
        return "
        flights.id,
        flights.origin,
        origin.name as origin_name,
        origin.short_name as origin_short_name,
        flights.destination,
        destination.name as destination_name,
        destination.short_name AS destination_short_name,
        flights.departure_date,
        DATE_FORMAT(flights.departure_time, '%H:%i') AS departure_time,
        DATE_FORMAT(flights.departure_min_time, '%H:%i') AS departure_min_time,
        DATE_FORMAT(flights.departure_max_time, '%H:%i') AS departure_max_time,
        flights.status,
        flights.created_by,
        users.complete_name as user_name,
        flights.created_at,
        flights.updated_at,
        flights.plane_id,
        planes.name AS plane_name,
        planes.seats_limit,
        COUNT(books.id) AS booked_seats,
        IF(flights.plane_id IS NULL, -1, (planes.seats_limit - COUNT(books.id))) AS availables_seats,
        IF(flights.departure_date >= '".date('Y-m-d')."', 'future', 'passed') as state
        ";
    }
    
    private static function rawForSelect(){
        return "
        flights.id,
        flights.origin,
        origin.name as origin_name,
        origin.short_name as origin_short_name,
        flights.destination,
        destination.name as destination_name,
        destination.short_name AS destination_short_name,
        flights.departure_date,
        DATE_FORMAT(flights.departure_time, '%H:%i') AS departure_time,
        DATE_FORMAT(flights.departure_min_time, '%H:%i') AS departure_min_time,
        DATE_FORMAT(flights.departure_max_time, '%H:%i') AS departure_max_time,
        flights.status,
        flights.created_by,
        users.complete_name as user_name,
        flights.created_at,
        flights.updated_at,
        flights.plane_id,
        planes.name AS plane_name,
        planes.seats_limit,
        COUNT(books.id) AS booked_seats,
        IF(flights.plane_id IS NULL, -1, (planes.seats_limit - COUNT(books.id))) AS availables_seats
        ";
    }
    
    /*
    * @deprecated
    */
    private static function fieldsForSelect() {
      
        return [
            'flights.id', 
            'flights.origin',
            'origin.name as origin_name',
            'origin.short_name as origin_short_name',
            'flights.destination',
            'destination.name as destination_name',
            'destination.short_name as destination_short_name',
            'flights.departure_date',
            'flights.departure_time',
            'flights.departure_max_time',
            'flights.departure_min_time',
            'flights.status',
            'flights.created_by',
            'flights.plane_id',
            'flights.created_at',
            'flights.updated_at',
            'planes.name as plane_name',
            'planes.seats_limit',
            'users.complete_name as user_name',
            'books.id'
        ];
    }
}