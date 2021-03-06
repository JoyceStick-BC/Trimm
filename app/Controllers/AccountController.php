<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use Carbon\Models\Payment;
use Carbon\Models\PublicKey;
use Carbon\Models\StripeDB;
use \Stripe\Stripe;
use \RandomLib\Factory;
use \SendGrid\Email;
use \SendGrid\Content;
use \SendGrid\Mail;

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
        StripeDB::updateOrCreate(
            ['user_id' => $this->auth->user()->id],
            ['card_id' => $customer->id]
        );

		$this->flash->addMessage('info', 'Payment information saved successfully');
		return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getCharge($request, $response, $args) {
        $factory = new Factory;
        $generator = $factory->getMediumStrengthGenerator();
        $key = $generator->generateString(6, '1234567890');

        $user = $this->auth->user();

        PublicKey::create(array(
            'user' => $user->username,
            'privateKey' => md5($key),
            'type' => 'charge',
            'expiration' =>  date("Y-m-d H:i:s", time() + 3600),
        ));

        var_dump($key);

        //FIGURE OUT EMAIL LATER
        /*$from = new Email($user->name, $user->email);
        $subject = 'Charge code';
        $to = new Email('Trimm', 'charges@trimm3d.com');
        $content = new Content('text/plain', 'Your charge key is: '. $key);
        $mail = new Mail($from, $subject, $to, $content);

        $sg = new \SendGrid(getenv('SENDGRID_API_KEY'));

        $response = $sg->client->mail()->send()->post($mail);

        echo "<pre>";
        var_dump($response);

        echo $response->statusCode();
        print_r($response->headers());
        echo $response->body();*/

        return $this->view->render($response, 'dashboard/confirmCharge.twig', [
            'bundle' => $args['bundleName'],
            'user' => $args['user'],
        ]);

    }

    public function postCharge($request, $response) {
        //check if the given key exists in the db
        $buyerUsername = $this->auth->user()->username;
        $key = PublicKey::select('privateKey', 'expiration')
               ->where('user', $buyerUsername)
               ->where('type', 'charge')
               ->where('privateKey', md5($request->getParam('key')))
               ->orderBy('id', 'desc')
               ->first();

        //if the keys do not match, exit
        if (!md5($request->getParam('key')) == $key->privateKey) {
            echo "Invalid Key";
            exit();
        } else if (strtotime($key->expiration) - time() > 3600) {
            echo "This key has expired";
            exit();
        }

        //get the bundle price based on username and bundlename
        $bundle_price = Bundle::select('price')
                        ->where('user', $request->getParam('sellerUsername'))
                        ->where('bundleName', $request->getParam('bundleName'))
                        ->first();
        $bundle_price = $bundle_price->price;

        $seller_id = User::select('id')->where('username', $request->getParam('sellerUsername'))->first();
        $bundleName = $request->getParam('bundleName');

        \Stripe\Stripe::setApiKey(getenv('STR_SEC'));
        //grab buyer card id and seller account id
        $buyer = StripeDB::select('card_id')
                       ->where('user_id', $this->auth->user()->id)
                       ->first();

        $seller = StripeDB::select('acct_id')
                        ->where('user_id', $seller_id->id)
                        ->first();

        //create charge on buyer with destination to seller bank account
        $charge = \Stripe\Charge::create(array(
            'amount' => $bundle_price,
            'currency' => 'usd',
            'customer' => $buyer->card_id,
            'destination' => array(
                'account' => $seller->acct_id,
            ),
        ));

        if ($charge->status == 'succeeded') {
            //insert record into db
            Payment::create(array(
                'buyer_card_id' => $buyer->card_id,
                'seller_acct_id' => $seller->acct_id,
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

        $has_account = StripeDB::select('acct_id')
                               ->where('user_id', $this->auth->user()->id)
                               ->first();

    	return $this->view->render($response, 'dashboard/bankInfo.twig', [
    		'pub_key' => $publishable_key,
            'has_account' => $has_account->acct_id,
    	]);
    }

    public function postBankInfo($request, $response) {
        Stripe::setApiKey(getenv('STR_SEC'));

        $username = $this->auth->user()->username;
        $acct = StripeDB::select('acct_id')
                        ->where('user_id', $this->auth->user()->id)
                        ->first();

        if (!$acct->acct_id) {
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
            StripeDB::updateOrCreate(
                ['user_id' => $this->auth->user()->id],
                ['acct_id' => $acct->id]
            );
        } else {
            //if the user already has an account, add the bank token to their account
            $acct = \Stripe\Account::retrieve($acct->acct_id);
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

    public function getBankHelp($request, $response) {
        //get account
        $acct = StripeDB::select('acct_id')
                        ->where('user_id', $this->auth->user()->id)
                        ->first();
        \Stripe\Stripe::setApiKey(getenv('STR_SEC'));
        $acct = \Stripe\Account::retrieve($acct->acct_id);
        //build array for twig
        $account_errors = array();
        if ($acct->verification->disabled_reason) {
            $account_errors['disabled_reason'] = $acct->verification->disabled_reason;
        } if ($acct->verification->fields_needed) {
            $account_errors['fields_needed'] = $acct->verification->fields_needed;
            $account_errors['due_by'] = $acct->verification->due_by;
        }

        return $this->view->render($response, 'dashboard/help.twig', $account_errors);
    }

}
