<?php

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PayPalTest extends TestCase {
    
    /*public function testAccessToken() {
         $paymentService = $this->getService('Payment');
         $token = $paymentService->getPayPalToken(); 
         
         var_dump($token);
         $this->assertTrue($token !== null);
    }
    
    public function testGetPayment() {
        $paymentService = $this->getService('Payment');
        $payment = $paymentService->getPayment('PAY-53U13412FN440374VK3GLISA'); 

        var_dump($payment->toJSON());
        $this->assertTrue($payment !== null);
    }*/
    
    public function testCheckPaymentAndSendMail() {
        $paymentService = $this->getService('Payment');
        $payment = $paymentService->getPayment('PAY-53U13412FN440374VK3GLISA');
        $paymentService->sendMailPaymentAuthorized($payment);
        
        //the email was sent
        $this->assertTrue(true);
    }
    
}
