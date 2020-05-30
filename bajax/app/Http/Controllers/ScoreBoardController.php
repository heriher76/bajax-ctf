<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\User;

class ScoreBoardController extends Controller
{
    public function index(){
        $data = User::orderBy('point','DESC')->orderBy('last_submit_flag','ASC')->get();
        return response()->json([
            'success' => true,
            'messages' => 'Score Board !',
            'data' => $data,
        ], 200);
    }
}
