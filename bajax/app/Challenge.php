<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
	protected $table = 'challenge';
    protected $fillable = [
        'name','point','note','flag','file1','file2','file3','file4',
    ];
}
