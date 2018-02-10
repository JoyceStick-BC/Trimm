<?php

namespace Carbon\Middleware;
use Carbon\Models\StripeDB;

class StripeMiddleware extends Middleware {
	public function __invoke($request, $response, $next) {
		$acct = StripeDB::select('acct_id')
						->where('user_id', $this->container->auth->user()->id)
						->first();
		if ($acct) {
			//if they have an account, check for possible errors with it
			\Stripe\Stripe::setApiKey(getenv('STR_SEC'));
			$acct = \Stripe\Account::retrieve($acct);
			if ($acct->verification->disabled_reason) {
				$this->container->flash->addMessage('error', "Your account has been disabled from receiving payment. Click <a href=" . $this->container->router->pathFor('account.help') . ">here</a> for more information.");
			} else if ($acct->verification->fields_needed) {
				$this->container->flash->addMessage('error', "Your account needs more info. Click <a href=" . $this->container->router->pathFor('account.help') . ">here</a> for more information.");
			}
		}
		return $next($request, $response);
	}
}