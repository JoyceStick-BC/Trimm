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
}