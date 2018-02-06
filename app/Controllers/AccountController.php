<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use Carbon\Models\Payment;
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

		$this->flash->addMessage('info', 'Payment information saved successfully');
		return $response->withRedirect($this->router->pathFor('home'));
    }

    public function postCharge($request, $response, $args) {
        //get these variables some other way. right now is done with get
        $buyerUsername = $args['buyerUsername'];
        $sellerUsername = $args['sellerUsername'];
        $bundle_price = $args['price'];
        $bundleName = $args['bundleName'];

        \Stripe\Stripe::setApiKey(getenv('STR_SEC'));
        //grab buyer card id and seller account id
        $buyer = User::select('stripe_card_id')
                       ->where('username', $buyerUsername)
                       ->first();

        $seller = User::select('stripe_acct_id')
                        ->where('username', $sellerUsername)
                        ->first();

        //create charge on buyer with destination to seller bank account
        $charge = \Stripe\Charge::create(array(
            'amount' => $bundle_price,
            'currency' => 'usd',
            'customer' => $buyer->stripe_card_id,
            'destination' => array(
                'account' => $seller->stripe_acct_id,
            ),
        ));
    
        if ($charge->status == 'succeeded') {
            //insert record into db
            Payment::create(array(
                'buyer_card_id' => $buyer->stripe_card_id,
                'seller_acct_id' => $seller->stripe_acct_id,
                'amount' => $bundle_price,
                'bundleName' => $bundleName,
            ));

            $this->flash->addMessage('info', 'Bundle purchased successfully.');
            return $response->withRedirect($this->router->pathFor('home'));
        } else {
            echo 'Unable to process payment:';
            var_dump($charge->failure_message);
        }
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
                'tos_acceptance' => array(
                    'date' => time(),
                    'ip' => $_SERVER['REMOTE_ADDR'],
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

        if ($acct->payouts_enabled) {
            $this->flash->addMessage('info', 'Bank account added successfully, ready to recieve payouts.');
            return $response->withRedirect($this->router->pathFor('home'));
        } else {
            echo "There was a problem with creating your account.";
            var_dump($acct->verification->disabled_reason);
        }
    }

}