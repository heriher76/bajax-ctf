<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Challenge;
use App\ChallengeLog;
use Auth;
use Carbon\Carbon;

class ChallengeLogController extends Controller
{
    public function cekFlag($id, Request $request){
    	$challenge=Challenge::find($id);
    	$challengeLog=ChallengeLog::where(['user_id' => Auth::id(),'challenge_id' => $id])->count();
    	if(!$challengeLog){
	    	if($challenge->flag === $request->input('flag')){
	    		$succsessChal=ChallengeLog::create([
	    			'user_id' => Auth::id(),
	    			'challenge_id' => $id,
	    		]);
                $point=Auth::user()->point+$challenge->point;
	    		User::find(Auth::id())->update(['last_submit_flag' => Carbon::now()->toDateTimeString(), 'point' => $point]);
                return response()->json([
                    'success' => true,
                    'messages' => 'Success, +'.$challenge->point.' Point, Your Point '.$point,
                    'data'=>NULL,
                ], 200);
	    	}
	    	else 
                return response()->json([
                    'success' => false,
                    'messages' => 'Wrong Flag !',
                    'data'=>NULL,
                ], 403);
    	}
    	else
            return response()->json([
                'success' => false,
                'messages' => 'You Already Finished This Challenge',
                'data'=>NULL,
            ], 400);
    }
}
