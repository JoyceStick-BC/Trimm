<?php

namespace Carbon\Validation\Rules;

use Carbon\Models\User;
use Respect\Validation\Rules\AbstractRule;

class EmailAvailable extends AbstractRule {
	public function validate($input) {
		return User::where('email', $input)->count() === 0;
	}
}