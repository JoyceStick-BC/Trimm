<?php
	namespace Carbon\Models;

	//extend eloquent to connect to DB
	use Illuminate\Database\Eloquent\Model;


	class User extends Model {
		//if the table isnt the plural of the class name, use this:
		protected $table = 'users';

		//define which columns can be written to
		protected $fillable = [
			'email',
			'name',
			'password'
		];

		public function setPassword($password) {
	        $this->update([
	            'password' => password_hash($password, PASSWORD_DEFAULT)
	        ]);
    	}
	}