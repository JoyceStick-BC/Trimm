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
    	User::where('username', $username)->update(array('stripe_card_id' => $customer->id));

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

        $username = $this->auth->user()->username;
        $acct = User::select('stripe_acct_id')->where('username', $username)->first();

        if (!$acct->stripe_acct_id) {
            //if the user does not have a stripe custom account, make it
            $birthday = $request->getParam('birthday');
            $birthday_year = substr($birthday, 0, 4);
            $birthday_month = substr($birthday, 5, 2);
            $birthday_day = substr($birthday, 8, 2);
            $acct = \Stripe\Account::create(array(
                'type' => 'custom',
                'country' => $request->getParam('country'),
                'email' => $this->auth->user()->email,
                'external_account' => $request->getParam('bank-token'),
                'legal_entity' => array(
                    'first_name' => $request->getParam('first-name'),
                    'last_name' => $request->getParam('last-name'),
                    'ssn_last_4' => $request->getParam('ssn_last_4'),
                    'type' => $request->getParam('legal-entity'),
                    'dob' => array(
                        'day' => $birthday_day,
                        'month' => $birthday_month,
                        'year' => $birthday_year,
                    ),
                    'address' => array(
                        'city' => $request->getParam('address-city'),
                        'line1' => $request->getParam('address-line-1'),
                        'postal_code' => $request->getParam('address-postal-code'),
                        'state' => $request->getParam('state'),
                    ),
                ),
            ));
            //add to db
            User::where('username', $username)->update(array('stripe_acct_id' => $acct->id));
        } else {
            //if the user already has an account, add the bank token to their account
            $acct = \Stripe\Account::retrieve($acct->stripe_acct_id);
            //this replaces the current account
            $acct->external_accounts->create(array('external_account' => $request->getParam('bank-token')));
        }

    	return $this->view->render($response, 'home.twig');
    }

}