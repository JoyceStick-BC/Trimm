<?php
namespace Carbon\Models;

//extend eloquent to connect to DB
use Illuminate\Database\Eloquent\Model;


class SocialMedia extends Model {
    //if the table isnt the plural of the class name, use this:
    protected $table = 'SocialMedia';

    //define which columns can be written to
    protected $fillable = [
        'user',
        'link',
        'platform',
    ];
}