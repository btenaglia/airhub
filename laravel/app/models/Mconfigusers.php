<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Mconfigusers extends BaseModel {
    protected $table = 'mconfigusers';
    
    public function getId() {
        return $this->id;
    }
    
    public static function getvaluesmconfigusers($iduser, $param) {
        return DB::table('mconfigusers')
                ->where('iduser','=', $iduser)
                ->where('parameter','=', $param)
                ->select('value')
                ->get();
    }

}
