<?php

namespace Carbon\Middleware;

class StripeMiddleware extends Middleware {
	public function __invoke($request, $response, $next) {
		if ($this->check()) {
			$acct = $this->container->auth->user()->stripe_acct_id;
			\Stripe\Stripe::setApiKey(getenv('STR_SEC'));
			$acct = \Stripe\Account::retrieve($acct);
			if ($acct) {
				if ($acct->verification->disabled_reason) {
					$this->container->flash->addMessage('error', 'Your account has been disabled from receiving payment. Click here for more information.');
				} else if ($acct->verification->fields_needed) {
					$this->container->flash->addMessage('error', 'Your account needs more info. Click here for more information.');
				}
			}
			$this->set();
		}
		return $next($request, $response);
	}

	public function check() {
		//return time when user was last warned about account errors
		if (!isset($_SESSION['stripe_check'])) {
			$_SESSION['stripe_check'] = time();
		}
		return time() - $_SESSION['stripe_check'] > 86400;
	}

	public function set() {
		//set time of most recent stripe warning
		$_SESSION['stripe_check'] = time();
	}
}