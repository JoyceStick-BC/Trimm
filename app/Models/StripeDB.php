<?php
namespace Carbon\Models;

//extend eloquent to connect to DB
use Illuminate\Database\Eloquent\Model;


class StripeDB extends Model {
    //if the table isnt the plural of the class name, use this:
    protected $table = 'stripe';

    //define which columns can be written to
    protected $fillable = [
        'id',
        'user_id',
        'card_id',
        'acct_id',
    ];
}