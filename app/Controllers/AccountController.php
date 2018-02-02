<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use \Stripe\Stripe;

class AccountController extends Controller {
	public function getPayment($request, $response) {
		$stripe = array(
		  "secret_key"      => "sk_test_BQokikJOvBiI2HlWgH4olfQ2",
		  "publishable_key" => "pk_test_6pRNASCoBOKtIshFeQd4XMUh"
		);

		Stripe::setApiKey($stripe['secret_key']);

        return $this->view->render($response, 'dashboard/payment.twig', [
        	'key' => $stripe['publishable_key'],
        ]);
    }

    public function postPayment($request, $response) {

    	//update later w/ real api key
    	\Stripe\Stripe::setApiKey("sk_test_BQokikJOvBiI2HlWgH4olfQ2");

    	//grab token for payment
    	$token = $request->getParam('stripeToken');
    	$email = $this->auth->user()->email;

    	$customer = \Stripe\Customer::create(array(
		  "email" => $email,
		  "source" => $token,
		));

    	//this may need to be removed if we are just creating a customer to store
    	//later we will query for $customer->id
    	
		$charge = \Stripe\Charge::create(array(
		  "amount" => 1000,
		  "currency" => "usd",
		  "customer" => $customer->id
		));

		//HERE: save $customer->id in a database for later

		//LATER: query for customer id and create charge again with:

		/*	
		$charge = \Stripe\Charge::create(array(
		  "amount" => 1500, // $15.00 this time
		  "currency" => "usd",
		  "customer" => $customer_id
		));
		*/
    }
}