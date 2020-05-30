<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChallengeLog extends Model
{
	protected $table = 'challenge_log';
    protected $fillable = [
        'user_id','challenge_id'
    ];
    public function challenge(){
    	return $this->belongsTo('App\Challenge', 'challenge_id');
    }
    public function user(){
    	return $this->belongsTo('App\User', 'user_id');
    }
}
