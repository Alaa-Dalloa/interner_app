<?php

namespace App\Http\Controllers;
use JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;
use App\Models\MyFile;
use DB;
use Symfony\Component\HttpFoundation\Response;
class GroupController extends Controller
{
    public function AddGroup (Request $request)
 {  
  $rules=array(
    "group_name"=>"required",
  );
  $validator=Validator::make($request->all() , $rules);
  if($validator->fails()){
    return $validator->errors();
  }
  else
  {
   $group = new Group;
   $group->group_name=$request->group_name;
   $result=$group->save();
   if($result){
    return ["Result"=>"data has been saved"];
 }
 return ["Result"=>"operation failed"];
 }
 
}

  public function index($groupId)
    {

   $group = Group::find($groupId);
   return $group->users()->get();
    
    }

        
 public function indexGroup()
 {
   $groups = Group::all();

  return response()->json($groups);
 }

public function addUserToGroup($groupId, $userId)
{
    if (auth()->user()->id == 1) {
        $group = Group::find($groupId);
        $user = User::find($userId);
        if (!$user || !$group) {
            return response()->json("User or group not found", 404);
        }
        if ($user->groups->count() >= 5) {
            return response()->json("User is already a member of maximum allowed groups", 422);
        }
        if ($user->groups->contains($group)) {
            return response()->json("User is already in the group", 422);
        }
        $user->groups()->attach($group);

        return response()->json("User added to the group successfully");
    } else {
        return ["error" => "You don't have permission to do this"];
    }
}

  public function GroupByPer()
  {
    $userId=auth()->user()->id;
    $user =User::find($userId);
    $group=$user->groups()->get();
    return response()->json($group);

  }


  public function removeUserFromGroup($groupId, $userId)
{
    if (auth()->user()->id == 1) {
        $user = User::find($userId);
        $group = Group::find($groupId);

        if (!$user || !$group) {
            return response()->json("User or group not found", 404);
        }

        if (!$user->groups->contains($group)) {
            return response()->json("User is not in the group", 422);
        }

        $reservedFiles = MyFile::where('user_id', $userId)->where('status', '=','reserved')->get();
        foreach ($reservedFiles as $file) {
          $file->status = 'free';
          $file->user_id = 'null';
          $file->save();
        }

        $user->groups()->detach($group);

        return response()->json("User removed from the group successfully");
    } else {
        return ["error" => "you don't have permission to do this"];
    }
}


  public function destroy($groupId)
  {
    
    if(auth()->user()->id==1)
    {
      $group = Group::find($groupId);

      if (!$group) {
          return response()->json("Group not found", 404);
      }

      
      if ($group->files()->where('status', 'reserved')->exists()) {
          return response()->json("Cannot delete the group. There are reserved files associated with it", 422);
      }
      
        // Detach all users from the group
        $group->users()->detach();


        // Detach all files from the group
        $group->files()->delete(); 


        // Delete the group
        $group->delete();

      return response()->json("Group deleted successfully");
  
   }
    else
    {
        return ["error"=>"you dont have permission to do this"];
    }
}

}




