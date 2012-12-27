<?php

//Load Paymill Library
require_once '../application/libraries/paymill.php';

class Test extends PHPUnit_Framework_TestCase
{
	/* --------------------------------------------------------------
     * TEST INFRASTRUCTURE
     * ------------------------------------------------------------ */
	public $paymill;
    
    const EMAIL         =   'test@test.com';
    const DESCRIPTION   =   'description';
    
    public function setUp()
    {
    	parent::setUp ();
    	
    	$config = array ('paymill_apiKey'		=>	'9d34a65002e48295ef8f55aa533f4655',
						'paymill_apiEndPoint'	=>	'https://api.paymill.com/v2/');
		
        $this->paymill = new Paymill($config);
    }

    public function tearDown()
    {
        // Nothing
    }

    public function testNewClient()
    {
        $new_client = $this->paymill->new_client(self::EMAIL, self::DESCRIPTION);
        $client = $this->paymill->get_client($new_client['id']);
        
        $expected = $new_client['id'];
        $actual = $client['id'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_client($new_client['id']);
    }
    
    public function testUpdateClient()
    {
        //setUp
        $new_client = $this->paymill->new_client();
        
        $update_client = $this->paymill->update_client($new_client['id'], self::EMAIL, self::DESCRIPTION);
        
        $expected = self::DESCRIPTION;
        $actual = $update_client['description'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_client($new_client['id']);
    }

    public function testNewCreditCardPayment()
    {
        $new_credit_card_payment = $this->paymill->new_payment_credit_card('098f6bcd4621d373cade4e832627b4f6');
        $credit_card_paymen = $this->paymill->get_payment($new_credit_card_payment['id']);
        
        $expected = $new_credit_card_payment['id'];
        $actual = $credit_card_paymen['id'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_payment($new_credit_card_payment['id']);
    }
    
    public function testNewDebitCardPayment()
    {
        $new_debit_card_payment = $this->paymill->new_payment_debit_card('debit', '86055500', '1234512345',
                                                                    'Max Mustermann', 'client_5b92430bc7cab82c67f8');
        $debit_card_paymen = $this->paymill->get_payment($new_debit_card_payment['id']);

        $expected = $new_debit_card_payment['id'];
        $actual = $debit_card_paymen['id'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_payment($new_debit_card_payment['id']);
    }
    
    public function testNewTransactionToken()
    {
        $new_transaction_token = $this->paymill->new_transaction_token('4200', 'EUR', '098f6bcd4621d373cade4e832627b4f6',
                                                                'Test transaction');
        $transaction = $this->paymill->get_transaction($new_transaction_token['id']);
        
        $expected = $new_transaction_token['id'];
        $actual = $transaction['id'];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testNewTransactionPayment()
    {
        $new_transaction_payment = $this->paymill->new_transaction_payment('4200', 'EUR', 'pay_f55cfa205e9fa088cf80',
                                                                'Test transaction', 'client_7f3b923c2f4d2e29c625');
        $transaction = $this->paymill->get_transaction($new_transaction_payment['id']);
        
        $expected = $new_transaction_payment['id'];
        $actual = $transaction['id'];
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testNewOffer()
    {
        $new_offer = $this->paymill->new_offer('4200', 'EUR', 'year', 'Test offer');
        $offer = $this->paymill->get_offer($new_offer['id']);
        
        $expected = $new_offer['id'];
        $actual = $offer['id'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_offer($new_offer['id']);
    }
    
    public function testUpdateOffer()
    {
        //setUp
        $new_offer = $this->paymill->new_offer('4200', 'EUR', 'year', 'Test offer');
        
        $update_offer = $this->paymill->update_offer($new_offer['id'], 'Test update offer');
        $offer = $this->paymill->get_offer($update_offer['id']);
        
        $expected = $update_offer['id'];
        $actual = $offer['id'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_offer($new_offer['id']);
    }
    
    public function testNewSubscription()
    {
        //setUp
        $new_offer = $this->paymill->new_offer('4200', 'EUR', 'year', 'Test offer');
        
        $new_subscription = $this->paymill->new_subscription('client_7c7b7caa42027e86c993', 
                                                        $new_offer['id'],
                                                        'pay_2089532f6508b0250b52');
        $subscription = $this->paymill->get_subscription($new_subscription['id']);
        
        $expected = $new_subscription['id'];
        $actual = $subscription['id'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_subscription($new_subscription['id']);
        $this->paymill->remove_offer($new_offer['id']);
    }
    
    public function testUpdateSubscription()
    {
        //setUp
        $new_offer = $this->paymill->new_offer('4200', 'EUR', 'year', 'Test offer');
        $new_subscription = $this->paymill->new_subscription('client_7c7b7caa42027e86c993', 
                                                        $new_offer['id'],
                                                        'pay_2089532f6508b0250b52');
        
        $update_subscription = $this->paymill->update_subscription($new_subscription['id'], true);
        $subscription = $this->paymill->get_subscription($update_subscription['id']);
        
        $expected = $update_subscription['id'];
        $actual = $subscription['id'];
        
        $this->assertEquals($expected, $actual);
        
        //tearDown
        $this->paymill->remove_subscription($new_subscription['id']);
        $this->paymill->remove_offer($new_offer['id']);
    }
}

