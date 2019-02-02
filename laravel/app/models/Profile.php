<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Profile extends BaseModel {
    protected $table = 'profiles';
    
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getTypes() {
        return $this->type;
    }
    
    public function setTypes($type) {
        $this->type = $type;
    }
    
    public function getHours() {
        return $this->hours;
    }
    
    public function setHours($hours) {
        $this->hours = $hours;
    }
    
    public function getSeats() {
        return $this->seats;
    }
    
    public function setSeats($seats) {
        $this->seats = $seats;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function setPrice($price) {
        $this->price = $price;
    }
    
    public static function all_custom() {
        return DB::table('profiles')
                ->where('profiles.active','=', 1)
                ->select(DB::raw(self::rawForSelectOrig()))
                ->get();
    }
    
    public static function find_custom($id) {
        return DB::table('profiles')
                ->where('profiles.active','=', 1)
                ->where('profiles.id','=', $id)
                ->select(DB::raw(self::rawForSelectOrig()))
                ->get();
    }
    
    public static function find_custom_last() {
        return DB::table('profiles')
                ->where('profiles.active','=', 1)
                ->orderBy('profiles.id', 'desc')
                ->select(DB::raw(self::rawForSelectOrig()))
                ->get();
    }
    
    private static function rawForSelectOrig(){
        return "
        profiles.id,
        profiles.name,
        profiles.type,
        profiles.hours,
        profiles.seats,
        profiles.price,
        profiles.state,
        profiles.active,
        profiles.created_at,
        profiles.updated_at
        ";
    }
    
    private static function _rawForSelectOrig(){
        return "
        profiles.id,
        profiles.name,
        IF(profiles.type=1, 'true', 'false') AS type,
        profiles.hours,
        profiles.seats,
        profiles.price,
        profiles.state,
        profiles.active,
        profiles.created_at,
        profiles.updated_at
        ";
    }
    
}

