<?php

/**
 * TODO Comment of component here!
 * 
 * <p><a href="AccountControllerTest.java.html"><i>View Source</i></a></p>
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class AccountControllerTest extends TestCase {
    
    public function testRegisterMethod() {
        $input = [
            'email' => 'admn@example.com',
            'password' => 'secret',
            'complete_name' => 'Emilio Genesio',
            'address' => 'Corrientes 661',
            'cell_phone' => '3245666',
            'body_weight' => '50',
            'user_type' => App\Models\User::USER_TYPE_APP,
        ];
        $response = $this->call('POST', '/register', $input);
        var_dump($response);
    }
    
    public function testLoginMethod() {
        $input = [
            'email' => 'admin@example.com',
            'password' => 'secret',
        ];
        $response = $this->call('POST', '/login', $input);
        var_dump($response);
    }
    
    public function testAuthRoute() {
        
    }
}
