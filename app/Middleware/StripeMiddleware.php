<?php

namespace Carbon\Middleware;

class StripeMiddleware extends Middleware {
	public function __invoke($request, $response, $next) {
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

		return $next($request, $response);
	}
}