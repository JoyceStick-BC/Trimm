<?php
namespace Carbon\Models;

//extend eloquent to connect to DB
use Illuminate\Database\Eloquent\Model;


class BundleComponent extends Model {
    //if the table isnt the plural of the class name, use this:
    protected $table = 'bundlecomponents';

    //define which columns can be written to
    protected $fillable = [
        'hash',
        'bundle_id',
        'path',
        'name',
    ];
}