<?php
namespace Carbon\Models;

//extend eloquent to connect to DB
use Illuminate\Database\Eloquent\Model;


class Bundle extends Model {
    //if the table isnt the plural of the class name, use this:
    protected $table = 'bundles';

    //define which columns can be written to
    protected $fillable = [
        'user',
        'bundleName',
        'hash',
        'version',
        'description'
    ];
}