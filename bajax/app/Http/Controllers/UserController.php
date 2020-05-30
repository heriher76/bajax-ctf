<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\ChallengeLog;
use Spatie\Permission\Models\Role;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:user-list');
         $this->middleware('permission:user-create', ['only' => ['store']]);
         $this->middleware('permission:user-edit', ['only' => ['update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'messages' => 'Data Users !',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users',
            'password' => 'required|min:8',
            'birthplace' => 'required|max:191', 
            'dateofbirth' => 'required|date_format:Y-m-d', 
            'address' => 'required',
            'website' => 'max:191',
            'roles' => 'required|exists:roles,name',
            'roles.*' => 'required|exists:roles,name',
            'visible' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Please fill in the blank !',
                'data' => $validator->errors(),
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
            'email_code' => str_random(40),
        ];
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        if($user){
            return response()->json([
                'success' => true,
                'messages' => 'Add User Success !',
                'data' => $user
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\t Insert Data !',
                'data'=>NULL,
            ], 400);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if($user){
            $challenges = ChallengeLog::where('user_id',$id)->get();

            $rank= User::where([
                ['point',$user->point??0],
                ['last_submit_flag','<=',$user->last_submit_flag??0],
            ])->orderBy('point','DESC')->orderBy('last_submit_flag','ASC')->count();

            $rank+= User::where('point','>',$user->point??0)->orderBy('point','DESC')->orderBy('last_submit_flag','ASC')->count();

            return response()->json([
                'success' => true,
                'messages' => 'Show User !',
                'data' => [
                    'user' => $user,
                    'role' => User::find($user->id)->getRoleNames(),
                    'challenges' => $challenges,
                    'rank' => $rank
                ]
            ], 200);
        }
        else
            return response()->json([
                'success' => false,
                'messages' => 'No User !',
                'data' => NULL
            ], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $oldUser=User::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email,'.Auth::id(),
            'password' => 'required|min:8',
            'birthplace' => 'required|max:191', 
            'dateofbirth' => 'required|date_format:Y-m-d', 
            'address' => 'required',
            'website' => 'max:191',
            'roles' => 'required|exists:roles,name',
            'roles.*' => 'required|exists:roles,name',
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

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('roles'));
        if($user){
            return response()->json([
                'success' => true,
                'messages' => 'Update User Success !',
                'data' => $user
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Update User !',
                'data'=>NULL,
            ], 400);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($id != 1){
            $user=User::find($id);
            if($user){
                $user->delete();
                return response()->json([
                    'success' => true,
                    'messages' => 'Delete User Success !',
                    'data'=>NULL,
                ], 200);
            }
            else
                return response()->json([
                    'success' => false,
                    'messages' => 'User Not Found !',
                    'data'=>NULL,
                ], 400);
        }
        else
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Delete User !',
                'data'=>NULL,
            ], 400);
    }
}