<?php

/**
 * Testing the account service
 * 
 * <p><a href="AccountServiceTest.java.html"><i>View Source</i></a></p>
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class AccountServiceTest extends TestCase {
    
    public function testAuthenticateByCredentials() {
        $accountService = $this->getService('Account');
        $credentials = [
            'email' => 'admin@example.com',
            'password' => 'secret'
        ];
        
        $token = $accountService->authenticateByCredentials($credentials);
        var_dump($token);

        $this->assertTrue($token != null);
    }
    
    public function testExistsUser() {
         $accountService = $this->getService('Account');
         $exists = $accountService->userExists('admiiiiiiiiin@example.com');
         
         $this->assertTrue($exists === true);
    }
}
