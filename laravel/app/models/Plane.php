<?php
namespace App\Models;

class Plane extends BaseModel {
    protected $table = 'planes';
    
    public function getId() {
        return $this->id;
    }
    
    public function getIdentifier() {
        return $this->identifier;
    }
    
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function setType($type) {
        $this->type = $type;
    }
    
    public function getSeatsLimit() {
        return $this->seats_limit;
    }
    
    public function setSeatsLimit($seatsLimit) {
        $this->seats_limit = $seatsLimit;
    }
    
    public function getWeightLimit() {
        return $this->weight_limit;
    }
    
    public function setWeightLimit($weightLimit) {
        $this->weight_limit = $weightLimit;
    }
    
    public function getFlights() {
        return $this->hasMany('App\Models\Flight');
    }
}

