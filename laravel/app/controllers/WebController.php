<?php

use App\Models\Setup;


class WebController extends BaseController implements GenericControllers {

    public function create() {
    }
    
    public function edit($id) {
    }
    
    public function destroy($id) {
    }
    
    public function all() {
    }
    
    public function find($id) {
    }
    
    
    
    public function eula() {
    	$SetupService = $this->getService('Setup');
      $terms = $SetupService->findme(2);
      return View::make('outputhtml', array('content' => $terms->paramtext1));
    }
    
    public function privacy() {
    	$SetupService = $this->getService('Setup');
      $terms = $SetupService->findme(3);
      return View::make('outputhtml', array('content' => $terms->paramtext1));
    }
  	
}
