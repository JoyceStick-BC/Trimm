<?php
namespace Carbon\Models;

//extend eloquent to connect to DB
use Illuminate\Database\Eloquent\Model;


class Payment extends Model {
    //if the table isnt the plural of the class name, use this:
    protected $table = 'payment';

    //define which columns can be written to
    protected $fillable = [
        'buyer_card_id',
        'seller_acct_id',
        'amount',
        'bundleName',
        'created_at',
    ];
}