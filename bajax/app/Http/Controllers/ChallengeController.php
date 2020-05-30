<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Challenge;
use App\ChallengeLog;
use App\WebConfig;
use Auth;
use Validator;

class ChallengeController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:challenge-create', ['only' => ['store']]);
         $this->middleware('permission:challenge-edit', ['only' => ['destroyFile','update']]);
         $this->middleware('permission:challenge-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listChallenges = Challenge::orderBy('id','ASC')->get();
        $challenges=array();
        foreach ($listChallenges as $challenge) {
            if(!Auth::user()->can('challenge-edit'))
                $challenge['flag']="SECRET";

            $challenge['file1']=($challenge['file1']!=NULL)?env('CHALLENGE_URL').'/'.$challenge['file1']:NULL;
            $challenge['file2']=($challenge['file2']!=NULL)?env('CHALLENGE_URL').'/'.$challenge['file2']:NULL;
            $challenge['file3']=($challenge['file3']!=NULL)?env('CHALLENGE_URL').'/'.$challenge['file3']:NULL;
            $challenge['file4']=($challenge['file4']!=NULL)?env('CHALLENGE_URL').'/'.$challenge['file4']:NULL;

            $challengeLog=ChallengeLog::where(['user_id' => Auth::id(),'challenge_id' => $challenge->id])->count();
            if($challengeLog)
                $challenges[]=array("data"=>$challenge,"finished"=>true);
            else 
                $challenges[]=array("data"=>$challenge,"finished"=>false);
        }
        return response()->json([
            'success' => true,
            'messages' => 'Data Roles !',
            'data' => $challenges,
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
            'point' => 'required|integer',
            'note' => 'required',
            'flag' => 'required|max:191',
            'file1' => 'max:2048',
            'file2' => 'max:2048',
            'file3' => 'max:2048',
            'file4' => 'max:2048',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Please fill in the blank !',
                'data' => $validator->errors(),
            ], 400);
        }

        $data=$request->all();        
        $input=[
            'name' => $data['name'],
            'point' => $data['point'],
            'note' => $data['note'],
            'flag' => $data['flag'],
        ];
        
        if($_FILES['file1']){
            $file1=$_FILES['file1']['name'];
            $input['file1']=$file1;
        }
        if($_FILES['file2']){
            $file2=$_FILES['file2']['name'];
            $input['file2']=$file2;
        }
        if($_FILES['file3']){
            $file3=$_FILES['file3']['name'];
            $input['file3']=$file3;
        }
        if($_FILES['file4']){
            $file4=$_FILES['file4']['name'];
            $input['file4']=$file4;
        }
        $idChallenge = Challenge::create($input);
        if($idChallenge){
            if($_FILES['file1']){
                $target = storage_path().'/app/public/'.$file1;
                move_uploaded_file( $_FILES['file1']['tmp_name'], $target);
            }
            if($_FILES['file2']){
                $target = storage_path().'/app/public/'.$file2;
                move_uploaded_file( $_FILES['file2']['tmp_name'], $target);
            }
            if($_FILES['file3']){
                $target = storage_path().'/app/public/'.$file3;
                move_uploaded_file( $_FILES['file3']['tmp_name'], $target);
            }
            if($_FILES['file4']){
                $target = storage_path().'/app/public/'.$file4;
                move_uploaded_file( $_FILES['file4']['tmp_name'], $target);
            }

            return response()->json([
                'success' => true,
                'messages' => 'Add Challenge Success !',
                'data' => $idChallenge
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Insert Challenge!',
                'data'=>NULL,
            ], 400);
        }
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
        $challenge = Challenge::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'point' => 'required|integer',
            'note' => 'required',
            'flag' => 'required|max:191',
            'file1' => 'max:2048',
            'file2' => 'max:2048',
            'file3' => 'max:2048',
            'file4' => 'max:2048',
        ]);
        if ($validator->fails() || !$challenge){
            return response()->json([
                'success' => false,
                'messages' => empty($validator->fails())?'Challenge Not Found !':'Please fill in the blank !',
                'data' => empty($validator->fails())?NULL:$validator->errors(),
            ], 400);
        }

        $data=$request->all();        
        $input=[
            'name' => $data['name'],
            'point' => $data['point'],
            'note' => $data['note'],
            'flag' => $data['flag'],
        ];
        
        if($request->file('file1')){
            $file1=$request->file('file1')->getClientOriginalName();
            $input['file1']=$file1;
        }
        if($request->file('file2')){
            $file2=$request->file('file2')->getClientOriginalName();
            $input['file2']=$file2;
        }
        if($request->file('file3')){
            $file3=$request->file('file3')->getClientOriginalName();
            $input['file3']=$file3;
        }
        if($request->file('file4')){
            $file4=$request->file('file4')->getClientOriginalName();
            $input['file4']=$file4;
        }
        WebConfig::where('name','update_point')->update(['value'=>'1']);
        $idChallenge = Challenge::find($id)->update($input);
        if($idChallenge){
            if($request->file('file1')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file1'), $file1);
            }
            if($request->file('file2')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file2'), $file2);
            }
            if($request->file('file3')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file3'), $file3);
            }
            if($request->file('file4')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file4'), $file4);
            }

            return response()->json([
                'success' => true,
                'messages' => 'Update Challenge Success !',
                'data' => NULL
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Update Challenge!',
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
        $challenge = Challenge::find($id);
       if($challenge){
            $challenge->delete();
            Storage::disk('challenges')->deleteDirectory($id);
            return response()->json([
                'success' => true,
                'messages' => 'Delete Challenge Success !',
                'data'=>NULL,
            ], 200);
        }
        else
            return response()->json([
                'success' => false,
                'messages' => 'Challenge Not Found !',
                'data'=>NULL,
            ], 400);
    }
    public function destroyFile($id,$file)
    {
        $challenge = Challenge::find($id);        
        if ($challenge) {
            if($challenge->{$file}){
                Storage::disk('challenges')->delete($id.'/'.$challenge->{$file});
                $challenge->update([$file => ""]);
                return response()->json([
                    'success' => true,
                    'messages' => 'Delete File Challenge Success !',
                    'data'=>NULL,
                ], 200);
            }
            else
                return response()->json([
                    'success' => false,
                    'messages' => 'File Challenge Not Found !',
                    'data'=>NULL,
                ], 400);
        }
        else
            return response()->json([
                'success' => false,
                'messages' => 'Challenge Not Found !',
                'data'=>NULL,
            ], 400);

    }
}
