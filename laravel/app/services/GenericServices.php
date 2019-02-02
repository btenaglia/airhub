<?php
namespace App\Services;
/**
 * Basic services here
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
interface GenericServices {
    
    public function create($input);
    
    public function edit($id, $input);
    
    public function destroy($id);
    
    public function all();
    
    public function find($id);
}
