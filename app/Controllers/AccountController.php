<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use \Stripe\Stripe;

class AccountController extends Controller {
	public function getPayment($request, $response) {
		$stripe = array(
		  "secret_key"      => getenv('STR_SEC'),
		  "publishable_key" => getenv('STR_PUB')
		);

		Stripe::setApiKey($stripe['secret_key']);

        return $this->view->render($response, 'dashboard/payment.twig', [
        	'key' => $stripe['publishable_key'],
        ]);
    }

    public function postPayment($request, $response) {
    	//update later w/ real api key
    	\Stripe\Stripe::setApiKey(getenv('STR_SEC'));

    	//grab token for payment
    	$token = $request->getParam('stripeToken');
    	$email = $this->auth->user()->email;
    	$username = $this->auth->user()->username;

    	var_dump($token);

    	$customer = \Stripe\Customer::create(array(
		  "email" => $email,
		  "source" => $token,
		));

    	User::where('username', $username)->update(array('stripe_id' => $customer->id));

		//LATER: query for customer id and create charge with:

		/*	
		$charge = \Stripe\Charge::create(array(
		  "amount" => 1500, // $15.00 this time
		  "currency" => "usd",
		  "customer" => $customer_id
		));
		*/

		$this->flash->addMessage('success', 'Payment information saved successfully');
		return $this->view->render($response, 'home.twig');
    }

    public function postCharge($request, $response) {
    	//get information about user (username/password/more?)

    	/*$username = $request->getParam('username');
    	$password = $request->getParam('password');

    	$id = User::select('stripe_id')->where('username', $username)->andWhere('password', $password)->get();
    	var_dump($id);*/
    }

    public function getBankInfo($request, $response) {
    	return $this->view->render($response, 'dashboard/bankInfo.twig');
    }

    public function postBankInfo($request, $response) {
    	var_dump($request->getParam('bank-token'));
    }

}