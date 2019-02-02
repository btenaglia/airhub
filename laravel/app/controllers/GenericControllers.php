<?php

/**
 * Implements this interface for the basic CRUD methods.
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
interface GenericControllers {
    
    public function create();
    
    public function edit($id);
    
    public function destroy($id);
    
    public function all();
    
    public function find($id);
}
