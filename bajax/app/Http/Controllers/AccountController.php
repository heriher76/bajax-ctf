<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;

class AccountController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $oldUser=Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email,'.Auth::id(),
            'password' => 'required|min:8',
            'birthplace' => 'required|max:191', 
            'dateofbirth' => 'required|date_format:Y-m-d', 
            'address' => 'required',
            'website' => 'max:191',
            'visible' => 'required',
        ]);
        if ($validator->fails() || !$oldUser){
            return response()->json([
                'success' => false,
                'messages' => empty($validator->fails())?'User  Not Found !':'Please fill in the blank !',
                'data' => empty($validator->fails())?NULL:$validator->errors(),
            ], 400);
        }


        $data = $request->all();
        $input=[
            'email' => $data['email'],
            'password' => Hash::make($data['password']),

            'name' => $data['name'],
            'birthplace' => $data['birthplace'],
            'dateofbirth' => $data['dateofbirth'],
            'aboutme' => $data['aboutme'],
            'address' => $data['address'],
            'website' => $data['website'],
            'visible' => $data['visible'],
        ];

        if($data['email'] != $oldUser->email){
            $input['email_code']=str_random(40);
        }
        if(!empty($data['password'])){ 
            $input['password'] = Hash::make($data['password']);
        }

        $user = User::find(Auth::id());
        $user->update($input);
        if($user){
            return response()->json([
                'success' => true,
                'messages' => 'Update Account Success !',
                'data' => $user
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Update Account !',
                'data'=>NULL,
            ], 400);
        }
    }
}
