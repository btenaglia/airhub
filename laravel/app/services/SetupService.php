<?php
namespace App\Services;

use App\Models\Setup;


class SetupService extends BaseService implements GenericServices {

    public function all() {
    }

    public function create($input) {
    }

    public function destroy($id) {
    }

    public function edit($id, $input) {
        //$setup = $this->find($id);
        //$setup = Setup::find($id);
        $setup = $this->findme($id);
        //$setup->setParamname($input['paramname']);
        
        switch($id){
         case 1:
          $setup->setParamvalueamount($input['paramvalueamount']);
          break;
         case 2:
         case 3:
          $paramtext = nl2br(htmlentities($input['paramtext1'], ENT_QUOTES, 'UTF-8'));
          $setup->setParamtext($paramtext);
          break;
         default:
          $paramtext = nl2br(htmlentities($input['paramtext1'], ENT_QUOTES, 'UTF-8'));
          $setup->setParamtext($paramtext);
        }
        
        $success = $setup->update();
        
        if($success) {
            return $setup;
        } else {
            return null;
        }
    }

    public function find($id) {
        //return Setup::find($id);
        return Setup::findmy($id);
        //return \App\Models\Setup::find($id);
    }
    
    public function findme($id) {
        return Setup::find($id);
        //return Setup::findmy($id);
        //return \App\Models\Setup::find($id);
    }
}
