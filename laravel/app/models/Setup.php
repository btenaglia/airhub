<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Setup extends BaseModel {
    protected $table = 'mconfig';
    
    //public static function find($id,$columns = array('*')) {
    public static function findmy($id) {
        //return Setup::where('id', '=', $id)->first();
        /*return DB::table('mconfig')
                ->select(DB::raw(self::rawForSelect()))
                ->get();*/
        return DB::table('mconfig')
                ->where('id', $id)
                ->select(DB::raw(self::rawForSelect()))
                ->first();        
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function getParamname() {
        return $this->paramname;
    }
    
    public function getParamvalueamount() {
        return $this->paramvalueamount;
    }
    
    public function setCode($code) {
        $this->code = $code;
    }
    
    public function setParamname($name) {
        $this->paramname = $name;
    }
    
    public function setParamvalueamount($amount) {
        $this->paramvalueamount = $amount;
    }
    
    public function getParamtext() {
        return $this->paramtext1;
    }
    
    public function setParamtext($text) {
        $this->paramtext1 = $text;
    }
    
    private static function rawForSelect(){
    	  /* \r\n */
        return "
            id,
            code,
            paramname,
            paramcode,
            paramvaluenum,
            paramvalueamount,
            paramvaluestring,
            paramvaluestring2,
            REPLACE(paramtext1, '<br />', '') AS paramtext1,
            observation,
            created_at,
            updated_at";
    }
    
    private static function __rawForSelect(){
    	  /* \r\n */
        return "
            id,
            code,
            paramname,
            paramcode,
            paramvaluenum,
            paramvalueamount,
            paramvaluestring,
            paramvaluestring2,
            REPLACE(paramtext1, '<br />', '\n') AS paramtext1,
            observation,
            created_at,
            updated_at";
    }
    
}

