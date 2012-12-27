<?php

// Load Paymill PHP API
require_once 'Paymill/lib/Services/Paymill/Base.php';
require_once 'Paymill/lib/Services/Paymill/Clients.php';
require_once 'Paymill/lib/Services/Paymill/Coupons.php';
require_once 'Paymill/lib/Services/Paymill/Exception.php';
require_once 'Paymill/lib/Services/Paymill/Offers.php';
require_once 'Paymill/lib/Services/Paymill/Payments.php';
require_once 'Paymill/lib/Services/Paymill/Refunds.php';
require_once 'Paymill/lib/Services/Paymill/Subscriptions.php';
require_once 'Paymill/lib/Services/Paymill/Transactions.php';
require_once 'Paymill/lib/Services/Paymill/Apiclient/Curl.php';
require_once 'Paymill/lib/Services/Paymill/Apiclient/Interface.php';

/**
 * Paymill PHP class
 * 
 * @todo
 *  - find type of created_at and trial_period_days
 * 
 * @author  Ignacio Soriano Cano y Pablo Sánchez Lozano
 * @version 1.0.0
 */
class Paymill {
	
	private $ci;
	
	private static $apiKey;
	private static $apiEndPoint;
    
    const CLIENTS       =   'clients';
    const PAYMENTS      =   'payments';
    const OFFERS        =   'offers';
    const REFUNDS       =   'refunds';
    const SUBSCRIPTIONS =   'subscriptions';
    const TRANSACTIONS  =   'transactions';

	function __construct($config = array())
	{
		if ( empty($config) )
		{	
			$this->ci =& get_instance();	//Load CodeIgniter
			
			if ($this->ci != NULL)
			{
				$this->ci->load->config('paymill');
				
				if ( ! $this->ci->config->item('paymill_test'))
				{
					self::$apiKey = $this->ci->config->item('paymill_apiKey');
					self::$apiEndPoint = $this->ci->config->item('paymill_apiEndPoint');
				}
				else 
				{
					self::$apiKey = $this->ci->config->item('paymill_apiKey_test');
					self::$apiEndPoint = $this->ci->config->item('paymill_apiEndPoint_test');
				}
				
			}
		}
		else
		{
			$this->initialize($config);
		}
	}

	/**
	 * Initialize Paymill library with $config arguments
	 */
	function initialize ($config = array())
	{
		extract($config);

		self::$apiKey = $config['paymill_apiKey'];
		self::$apiEndPoint = $config['paymill_apiEndPoint'];
	}

	/**
	 * Authenticate at the Paymill API
     * Service param select the service to construct
	 *
     * @param   service
     * @return  object
	 */
	function authentication($service)
	{
		$apiKey = self::$apiKey;
		$apiEndpoint = self::$apiEndPoint;
        
        switch ($service) {
            case self::CLIENTS:
                return new Services_Paymill_Clients($apiKey, $apiEndpoint);
                break;
            case self::PAYMENTS:
                return new Services_Paymill_Payments($apiKey, $apiEndpoint);
                break;
            case self::OFFERS:
                return new Services_Paymill_Offers($apiKey, $apiEndpoint);
                break;
            case self::REFUNDS:
                return new Services_Paymill_Refunds($apiKey, $apiEndpoint);
                break;
            case self::SUBSCRIPTIONS:
                return new Services_Paymill_Subscriptions($apiKey, $apiEndpoint);
                break;
            case self::TRANSACTIONS:
                return new Services_Paymill_Transactions($apiKey, $apiEndpoint);
                break;
            default:
                return new Services_Paymill_Clients($apiKey, $apiEndpoint);
                break;
        }
		
	}
	
	/**
	 * Creates a credit card payment from a given token,
	 * if you’re providing the client-property, the payment
	 * will be created and subsequently be added to the client.
	 *
	 * @param  string, token
	 * @param  string, client *optional
	 * @return array, data credit card payment 
	 */
	function new_payment_credit_card($token, $client = NULL)
	{
		$params = array(
		    'token'	  =>  $token,
		    'client'  =>  $client
		);
			
		$paymentsObject = $this->authentication(self::PAYMENTS);
		
		return $paymentsObject->create($params);
	}
	
	/**
	 * Creates a direct debit payment from the given account data,
	 * if you’re providing the client-property, the payment will 
	 * be created and subsequently be added to the client.
	 *
	 * @param  string, type
	 * @param  string, code
	 * @param  string, account
	 * @param  string, holder
	 * @param  string, client *optional
	 * @return array, data debit card payment 
	 */
	function new_payment_debit_card($type, $code, $account, $holder, $client = NULL)
	{
		$params = array(
		    'type'    =>  $type,
		    'code'    =>  $code,
		    'account' =>  $account,
		    'holder'  =>  $holder,
		    'client'  =>  $client
		);
		
		$paymentsObject = $this->authentication(self::PAYMENTS);
		
		return $paymentsObject->create($params);
	}
	
	/**
	 * Returns data of a specific payment.
	 *
	 * @param  string, payment id
	 * @return array, data of payment 
	 */
	function get_payment($id)
	{
	    $paymentsObject = $this->authentication(self::PAYMENTS);
        
		return $paymentsObject->getOne($id);
	}
	
	/**
     * Get payment list
     * 
     * @param   int, count *optional
     * @param   int, offset *optional
     * @param   ¿?, created_at *optional
     * @return  array, list payments
     */
	function get_list_payment($count = NULL, $offset = NULL, $created_at = NULL)
	{
	    $params = array(
           'count'         =>  $count,
           'offset'        =>  $offset,
           'created_at'    =>  $created_at
        );
        
	    $paymentsObject = $this->authentication(self::PAYMENTS);
        
		return $paymentsObject->get($params);
	}
	
	/**
	 * Deletes the specified payment.
	 *
	 * @param  string, payment id
	 * @return array, empty 
	 */
	function remove_payment($id)
	{
	    $paymentsObject = $this->authentication(self::PAYMENTS);
        
		return $paymentsObject->delete($id);
	}
	
	/**
	 * Create a transaction from token.
	 * You have to create at least either a token object
	 * before you can execute a transaction. You get back a response
	 * object indicating whether a transaction was successful or not.
	 *
	 * @param  integer, amount
	 * @param  string, currency
	 * @param  string, token
	 * @param  string, description *optional
	 * @return array, transaction data 
	 */
	function new_transaction_token($amount, $currency, $token, $description = NULL)
	{
		$params = array(
		    'amount' 		=>	$amount,
		    'currency' 		=>	$currency,
		    'token' 		=>	$token,
		    'description' 	=>	$description
		);
		
		$transactionsObject = $this->authentication(self::TRANSACTIONS);
		
		return $transactionsObject->create($params);
	}
	
	/**
	 * Create a transaction from payment.
	 * You have to create at least either a payment object
	 * before you can execute a transaction. You get back a response
	 * object indicating whether a transaction was successful or not.
	 *
	 * @param  integer, amount
	 * @param  string, currency
	 * @param  string, payment
	 * @param  string, description *optional
	 * @param  string, client *optional
	 * @return array, transaction data 
	 */
	function new_transaction_payment($amount, $currency, $payment, $description = NULL, $client = NULL)
	{
		$params = array(
		    'amount'      =>  $amount,
		    'currency'    =>  $currency,
		    'payment'     =>  $payment,
		    'description' =>  $description,
		    'client'      =>  $client
		);
		
		$transactionsObject = $this->authentication(self::TRANSACTIONS);
		
		return $transactionsObject->create($params);
	}
	
	/**
	 * Get a transaction object with the information of the used payment,
	 * client and transaction attributes.
	 *
	 * @param  string, id
	 * @return array, transaction data 
	 */
	function get_transaction($id)
	{
	    $transactionsObject = $this->authentication(self::TRANSACTIONS);
        
		return $transactionsObject->getOne($id);
	}
	
	/**
     * Get transaction list
     * 
     * @param   int, count *optional
     * @param   int, offset *optional
     * @param   ¿?, created_at *optional
     * @return  array, transaction list
     */
	function get_list_transaction($count = NULL, $offset = NULL, $created_at = NULL)
	{
	    $params = array(
           'count'         =>  $count,
           'offset'        =>  $offset,
           'created_at'    =>  $created_at
        );
        
	    $transactionsObject = $this->authentication(self::TRANSACTIONS);
        
		return $transactionsObject->get($param);
	}
	
	/**
	 * Refunds a transaction that has been created previously and
	 * was refunded in parts or wasn’t refunded at all. The inserted
	 * amount will be refunded to the credit card / direct debit of
	 * the original transaction. 
	 * There will be some fees for the merchant for every refund.
	 *
	 * @param  string, transaction id
	 * @param  integer, amount
	 * @param  string, description *optional
	 * @return array, refund transaction data 
	 */
	function refund_transaction($transactionId, $amount, $description = NULL)
	{
		$params = array(
		  'transactionId'     =>  $transactionId,
		  'params'            =>  array(
		      'amount'        =>  $amount,
		      'description'   =>  $description
          )
		    
		);
		
		$refundsObject = $this->authentication(self::REFUNDS);
		
		return $refundsObject->create($params);
	}
	
	/**
	 * Returns detailed informations of a specific refund.
	 *
	 * @param  string, refund id
	 * @return array, refund data 
	 */
	function get_refund($id)
	{
	    $refundsObject = $this->authentication(self::REFUNDS);
        
		return $refundsObject->getOne($id);
	}
	
	/**
     * Get refund list
     * 
     * @param   int, count *optional
     * @param   int, offset *optional
     * @param   string, transaction *optional
     * @param   string, client *optional
     * @param   int, amount *optional
     * @param   ¿?, created_at *optional
     * @return  array, transaction list
     */
	function get_list_refund($count = NULL, $offset = NULL, $transaction = NULL, $client = NULL,
                        $amount = NULL, $created_at = NULL)
	{
	    $params = array(
           'count'          =>  $count,
           'offset'         =>  $offset,
           'transaction'    =>  $transaction,
           'client'         =>  $client,
           'amount'         =>  $amount,
           'created_at'     =>  $created_at
        );
        
	    $refundsObject = $this->authentication(self::REFUNDS);
        
        return $refundsObject->get($params);
	}
	
	/**
	 * Creates a client object.
	 *
	 * @param  string, email *optional
	 * @param  string, description *optional
	 * @return array, client data 
	 */
	function new_client($email = NULL, $description = NULL)
	{
		$params = array(
		    'email'			=>	$email,
		    'description'	=>	$description
		);
		
		$clientsObject = $this->authentication(self::CLIENTS);
		
		return $clientsObject->create($params);
	}
	
	/**
	 * Get a client object.
	 *
	 * @param  string, client id
	 * @return array, client data 
	 */
	function get_client($id)
	{
	    $clientsObject = $this->authentication(self::CLIENTS);
        
		return $clientsObject->getOne($id);
	}
	
	/**
	 * Updates the data of a client. 
	 * To change only a specific attribute you can set this attribute
	 * in the update request.
	 * All other attributes that shouldn’t be edited aren’t inserted.
	 *
	 * @param  string, client id
	 * @param  string, email *optional
	 * @param  string, description *optional
	 * @return array, client data 
	 */
	function update_client($id, $email = NULL, $description = NULL)
	{
		$params = array(
		    'id'			=>	$id,
		    'email'			=>	$email,
		    'description'	=>	$description
		);
		
        $clientsObject = $this->authentication(self::CLIENTS);
        
		return $clientsObject->update($params);
	}
	
	/**
	 * Deletes a client, but your transactions aren’t deleted.
	 * 
	 * @param  string, client id
	 * @return array, client data
	 */
	function remove_client($id)
	{
		$clientsObject = $this->authentication(self::CLIENTS);
		
		return $clientsObject->delete($id);
	}
	
	/**
     * Get client list
     * 
     * @param   int, count *optional
     * @param   int, offset *optional
     * @param   string, creditcard *optional
     * @param   string , email *optional
     * @param   ¿?, created_at *optional
     * @return   array, list clients
     */
	function get_list_client($count = NULL, $offset = NULL, $creditcard = NULL, $email = NULL, $created_at = NULL)
	{
	    $params = array(
	       'count'         =>  $count,
	       'offset'        =>  $offset,
	       'creditcard'    =>  $creditcard,
	       'email'         =>  $email,
	       'created_at'    =>  $created_at
        );
        
	    $clientsObject = $this->authentication(self::CLIENTS);
        
		return $clientsObject->get($params);
	}
	
	/**
	 * Create an offer.
	 * 
	 * @param  integer, amount
	 * @param  string, current
	 * @param  enum (week, month, year), interval
	 * @param  string, name
	 * @return array, offer data
	 */
	function new_offer($amount, $currency, $interval, $name)
	{
		$params = array(
		    'amount'	=>	$amount,
		    'currency'	=>	$currency,
		    'interval'	=>	$interval,
		    'name'		=>	$name
		);
		
		$offersObject = $this->authentication(self::OFFERS);
		
		return $offersObject->create($params);
	}
	
	/**
	 * Getting detailed information about an offer requested with the offer ID.
	 * 
	 * @param  string, offer id
	 * @return array, offer data
	 */
	function get_offer($id)
	{
	    $offersObject = $this->authentication(self::OFFERS);
        
		return $offersObject->getOne($id);
	}
	
	/**
	 * Updates the offer. Only the name can be changed all other attributes cannot be edited
	 * 
	 * @param  string, offer id
	 * @param  string, name
	 * @return array, offer data
	 */
	function update_offer($id, $name)
	{
		$params = array(
		    'id'	=>	$id,
		    'name'	=>	$name
		);
        
        $offersObject = $this->authentication(self::OFFERS);
		
		return $offersObject->update($params);
	}
	
	/**
	 * Remove offer.
	 * You only can delete an offer if no client is subscribed to this offer.
	 * 
	 * @param  string, offer id
	 * @return array, empty
	 */
	function remove_offer($id)
	{
		$offersObject = $this->authentication(self::OFFERS);
		
		return $offersObject->delete($id);
	}
	
	/**
     * Get offer list
     * 
     * @param   int, count *optional
     * @param   int, offset *optional
     * @param   string, interval *optional
     * @param   int, amount *optional
     * @param   ¿?, created_at *optional
     * @param   ¿?, trial_period_days *optional
     * @return  array, offer list
     */
	function get_list_offer($count = NULL, $offset = NULL, $interval = NULL, $amount = NULL,
                        $created_at = NULL, $trial_period_days = NULL)
	{
	     $params = array(
           'count'              =>  $count,
           'offset'             =>  $offset,
           'interval'           =>  $interval,
           'amount'             =>  $amount,
           'created_at'         =>  $created_at,
           'trial_period_days'  =>  $trial_period_days
        );
        
	    $offersObject = $this->authentication(self::OFFERS);
        
		return $offersObject->get($params);
	}
	
	/**
	 * Creates a subscription between a client and an offer. 
	 * A client can have several subscriptions to different offers,
	 * but only one subscription to the same offer. The clients is
	 * charged for each billing interval entered.
	 * 
	 * @param  string, client
	 * @param  string, offer
	 * @param  string, payment
	 * @return array, subscription data
	 */
	function new_subscription($client, $offer, $payment) //payment??
	{
		$params = array(
		    'client'	=>	$client,
		    'offer'		=>	$offer,
		    'payment'	=>	$payment
		);
		
		$subscriptionsObject = $this->authentication(self::SUBSCRIPTIONS);
		
		return $subscriptionsObject->create($params);
	}
	
	/**
	 * Returns the detailed information of the concrete requested subscription.
	 * 
	 * @param  string, subscription id
	 * @return array, subscription data
	 */
	function get_subscription($id)
	{
        $subscriptionsObject = $this->authentication(self::SUBSCRIPTIONS);
        
		return $subscriptionsObject->getOne($id);
	}
	
	/**
	 * Updates the subscription of a client.
	 * 
	 * @param  string, subscription id
	 * @param  boolean, Cancel this subscription immediately or at the end of the current period
	 * @return array, subscription data
	 */
	function update_subscription($id, $cancel_at_period_end)
	{
		$params = array(
		    'id'					=>	$id,
		    'cancel_at_period_end'	=>	$cancel_at_period_end
		);
		
        $subscriptionsObject = $this->authentication(self::SUBSCRIPTIONS);
        
		return $subscriptionsObject->update($params);
	}
	
	/**
	 * Removes an existing subscription. If you set the attribute
	 * cancel_at_period_end parameter to the value true, the subscription
	 * will remain active until the end of the period. The subscription
	 * will not be renewed again. If the value is set to false it is directly
	 * terminated but pending transactions will still be charged.
	 * 
	 * @param  string, subscription id
	 * @return array, subscription data
	 */
	function remove_subscription($id)
	{
		$subscriptionsObject = $this->authentication(self::SUBSCRIPTIONS);
		
		return $subscriptionsObject->delete($id);
	}
	
	/**
     * Get subscription list
     * 
     * @param   int, count *optional
     * @param   int, offset *optional
     * @param   string, offer *optional
     * @param   ¿?, canceled_at *optional
     * @param   ¿?, created_at *optional
     * @return  array, subscription list
     */
	function get_list_subscription($count = NULL, $offset = NULL, $offer = NULL, 
	                           $canceled_at = NULL, $created_at = NULL)
	{
	    $params = array(
           'count'              =>  $count,
           'offset'             =>  $offset,
           'interval'           =>  $interval,
           'amount'             =>  $amount,
           'created_at'         =>  $created_at,
           'trial_period_days'  =>  $trial_period_days
        );
        
	    $subscriptionsObject = $this->authentication(self::SUBSCRIPTIONS);
        
		return $subscriptionsObject->get($params);
	}
}

/* End of file paymill.php */
/* Location: ./libraries/paymill.php */