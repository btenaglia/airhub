<?php
namespace App\Models;

/**
 * The base model functionality.
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class BaseModel extends \Eloquent {
    public function isActive() {
        return $this->active;
    }
    
    public function setActive($active) {
        $this->active = $active;
    }
    
    public static function findAllActives($modelNameString) {
        return $modelNameString::where('active','=', 1)->get();
    }
}
