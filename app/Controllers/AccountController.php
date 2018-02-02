<?php

namespace Carbon\Controllers;
use \Slim\Views\Twig as View;
use Carbon\Models\User;
use Carbon\Models\Bundle;
use \Stripe\Stripe;

class AccountController extends Controller {
	public function getPayment($request, $response) {
        return $this->view->render($response, 'dashboard/payment.twig');
    }
}