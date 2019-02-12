<?php
namespace App\Models;

class Place extends BaseModel {
    protected $table = 'places';
    protected $fillable = ['latitude','longitude'];
    
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getShortName() {
        return $this->short_name;
    }
    
    public function setShortName($shortName) {
        $this->short_name = $shortName;
    }
    
    public function getOriginFlight() {
        return $this->hasMany('App\Models\Flight');
    }
    
    public function getDestinationFlight() {
        return $this->hasMany('App\Models\Flight');
    }
}

