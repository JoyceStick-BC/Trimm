<?php
	namespace Carbon\Models;

	//extend eloquent to connect to DB
	use Illuminate\Database\Eloquent\Model;
	use Carbon\models\following;

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

    	public function getFollowings(){
    		 $followingids = Following::where('primaryUser', $this->id)->get();
    		 $following = array(); 
    		 foreach ($followingids as $followingid) {
    		 	$following[] = User::where('id', $followingid->referenceUser)->first();
    		 }
    		 return $following;
    	}

    	public function getFollowers(){
    		$followerids = Followers::where('referenceUser', $this->id)->get();
    		$followers = array(); 
    		foreach ($followerids as $followerid) {
    		 	$followers[] = User::where('id', $followerid->primaryUser)->first();
    		 }
    		 return $followers;

    	}
	}

