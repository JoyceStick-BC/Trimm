<?php

namespace Carbon\Middleware;

class StripeMiddleware extends Middleware {
	public function __invoke($request, $response, $next) {
		if ($this->check()) {
			//if its been greater than 12 hours since a users last stripe warning, check if they have a stripe account
			$acct = $this->container->auth->user()->stripe_acct_id;
			\Stripe\Stripe::setApiKey(getenv('STR_SEC'));
			$acct = \Stripe\Account::retrieve($acct);
			if ($acct) {
				//if they have an account, check for possible errors with it
				if ($acct->verification->disabled_reason) {
					$this->container->flash->addMessage('error', 'Your account has been disabled from receiving payment. Click <a href="{{ path_for("account.help")}}">here</a> for more information.');
				} else if ($acct->verification->fields_needed) {
					$this->container->flash->addMessage('error', 'Your account needs more info. Click <a href="{{ path_for("account.help")}}">here</a> for more information.');
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
		return time() - $_SESSION['stripe_check'] > 43200;
	}

	public function set() {
		//set time of most recent stripe warning
		$_SESSION['stripe_check'] = time();
	}
}