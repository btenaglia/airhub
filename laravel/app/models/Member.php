<?php
namespace App\Models;

class Member extends BaseModel
{
   
    protected $table = 'members';
    protected $fillable = ['description','discount'];
   
}
