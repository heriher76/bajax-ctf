<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebConfig extends Model
{
	protected $table = 'WebConfig';
    protected $fillable = [
        'name','value'
    ];
}
