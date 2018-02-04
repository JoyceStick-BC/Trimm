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
			'password',
            'username'
		];

		public function setPassword($password) {
	        $this->update([
	            'password' => password_hash($password, PASSWORD_DEFAULT)
	        ]);
    	}

    	public function getStars(){
    		$starred_ids = Stars::where('user', $this->id)->get();

    		$starredBundles = array();
    		foreach ($starred_ids as $starred_id){
    			$starredBundles[] = Bundle::where('id', $starred_id->bundle_id)->first();
    		}
    		return $starredBundles;
    	}
	}
