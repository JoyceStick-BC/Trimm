<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use \Stripe\Stripe;

class AccountController extends Controller {
	public function getPayment($request, $response) {
		//get form key for Stripe
		$publishable_key = getenv('STR_PUB');

        return $this->view->render($response, 'dashboard/payment.twig', [
        	'pub_key' => $publishable_key,
        ]);
    }

    public function postPayment($request, $response) {
    	//get form key for Stripe
    	\Stripe\Stripe::setApiKey(getenv('STR_SEC'));

    	//grab token for payment
    	$token = $request->getParam('stripeToken');
    	$email = $this->auth->user()->email;
    	$username = $this->auth->user()->username;

    	$customer = \Stripe\Customer::create(array(
		  "email" => $email,
		  "source" => $token,
		));
    	
    	//update the user's payment key in the db
    	User::where('username', $username)->update(array('stripe_id' => $customer->id));

		//LATER: query for customer id and create charge with:

		/*	
		\Stripe\Stripe::setApiKey(getenv('STR_SEC'));

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
    	//get form key for stripe
    	$publishable_key = getenv('STR_PUB');

    	return $this->view->render($response, 'dashboard/bankInfo.twig', [
    		'pub_key' => $publishable_key,
    	]);
    }

    public function postBankInfo($request, $response) {
        Stripe::setApiKey(getenv('STR_SEC'));

        $acct = \Stripe\Account::create(array(
            'type' => 'custom',
            'country' => $request->getParam('country'),
            'email' => $this->auth->user()->email,
            'external_account' => $request->getParam('bank-token'),
        ));

    	$username = $this->auth->user()->username;

    	//update account id in db
    	User::where('username', $username)->update(array('stripe_acct_id' => $acct->id));

    	return $this->view->render($response, 'home.twig');
    }

}