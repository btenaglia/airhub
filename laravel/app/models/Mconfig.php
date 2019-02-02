<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Mconfig extends BaseModel {
    protected $table = 'mconfig';
    
    public function getId() {
        return $this->id;
    }
    
    public static function getvaluesmconfig($fieldf, $valuef, $fieldreturn) {
        return DB::table('mconfig')
                ->where($fieldf,'=', $valuef)
                ->select($fieldreturn)
                ->get();
    }

}
