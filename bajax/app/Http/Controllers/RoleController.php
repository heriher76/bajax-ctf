<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Validator;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:role-list');
         $this->middleware('permission:role-create', ['only' => ['store']]);
         $this->middleware('permission:role-edit', ['only' => ['update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','ASC')->get();
        return response()->json([
            'success' => true,
            'messages' => 'Data Roles !',
            'data' => $roles,
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
            'name' => 'required|unique:roles,name',
            'permission' => 'required|exists:permissions,name',
            'permission.*' => 'required|exists:permissions,name',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Please fill in the blank !',
                'data' => $validator->errors(),
            ], 400);
        }

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        if($role){
            return response()->json([
                'success' => true,
                'messages' => 'Add Role Success !',
                'data' => $role
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Insert Role!',
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
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        if($role)
            return response()->json([
                'success' => true,
                'messages' => 'Show Role !',
                'data' => [
                    'role' => $role,
                    'permission' => $rolePermissions
                ]
            ], 200);
        else
            return response()->json([
                'success' => false,
                'messages' => 'No Role !',
                'data' => NULL
            ], 400);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permission()
    {
        $permission = Permission::get();
         return response()->json([
            'success' => true,
            'messages' => 'Data Permission !',
            'data' => $permission
        ], 200);
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
        $role = Role::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,'.$id,
            'permission' => 'required|exists:permissions,name',
            'permission.*' => 'required|exists:permissions,name',
        ]);
        if ($validator->fails() || !$role){
            return response()->json([
                'success' => false,
                'messages' => empty($validator->fails())?'Role Not Found !':'Please fill in the blank !',
                'data' => empty($validator->fails())?NULL:$validator->errors(),
            ], 400);
        }

        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));

        if($role){
            return response()->json([
                'success' => true,
                'messages' => 'Update Role Success !',
                'data' => $role
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Update Role !',
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
        if($id and $id != 2 and $id != 3 and $id != 4){
            $role=DB::table("roles")->where('id',$id);
            if($role)
                $role->delete();
                return response()->json([
                    'success' => true,
                    'messages' => 'Delete Role Success !',
                    'data'=>NULL,
                ], 200);
            else
                return response()->json([
                    'success' => false,
                    'messages' => 'Can\'t Delete Role !',
                    'data'=>NULL,
                ], 400);
        }
        else
            return response()->json([
                'success' => false,
                'messages' => 'Role Not Found !',
                'data'=>NULL,
            ], 400);
    }
}