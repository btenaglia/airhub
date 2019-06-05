<?php
namespace App\Models;

class Message extends BaseModel
{
   
    protected $table = 'messages';
    protected $fillable = ['message'];
   
    public function getMember() {
        return $this->belongsTo('App\Models\Member', 'member_id');
    }
    
}
