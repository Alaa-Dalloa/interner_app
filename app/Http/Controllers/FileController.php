<?php

namespace App\Http\Controllers;
use JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Validator;
use App\Models\MyFile;
use App\Models\Report;
use App\Models\Group;
use DB;
use Carbon\Carbon;
use App\Aspects\Transactional;
class FileController extends Controller
{
public function uploadFile(Request $request)
{
    // Query to find the current user
    $user = auth()->user();

    if (!$user) {
        return response()->json("User not authenticated", 401);
    }

    // Query to find the group based on the ID
    $group = Group::find($request->groupId);

    if (!$group) {
        return response()->json("Group not found", 404);
    }

    // Check if the user has access to the group
    if (!$user->groups->contains($group)) {
        return response()->json("You do not have access to this group", 403);
    }

    $file = $request->file('link');

    // Check file size
    $fileSize = $file->getSize();
    $maxFileSize = 15 * 1024 * 1024; 

    if ($fileSize > $maxFileSize) {
        return response()->json(" the File size exceeds the maximum limit of file size", 400);
    }

    $fileName = time().$file->getClientOriginalName();
    $path = $file->storePubliclyAs('public/upload', $fileName);
    $link = Storage::url($path);
    $file = new MyFile;
    $file->link = $link;
    $file->file_name = $fileName;
    $file->status = $request->status;
    $file->user_id = 'null';
    $file->group_id = $group->id;
    $result1 = $file->save();

    $report = new Report;
    $report->user_name = auth()->user()->name;
    $report->file_name = $fileName;
    $report->group_name = $group->group_name;
    $report->the_operation = 'upload file';
    $report->the_time = Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d H:i');
    $result2 = $report->save();

    if ($result1 && $result2) {
        return response()->json("Operation file upload and report done!", 200);
    }
}
    public function reserveFile($id )
    {
    $file = MyFile::find($id);

    if (!$file) {
         return response()->json([
        'message' => 'File was not found'
        ], 400);
    }

    if ($file->status=='reserved') {
        return response()->json([
     'message' => 'File already reserved!!!!!'
    ], 400);
    }
    $file->status = 'reserved';
    $result=$file->save();
     if($result)
      {
        return ["Result"=>"file has been reserved"];
      }
     return ["Result"=>"operation failed"];
    }

   public function indexFiles()
  {  
    
    $files = MyFile::all();

  return response()->json($files);
   
  }

#[Transactional(expiration: 10, maxAttempting: 5)]
public function reserveFiles(Request $request, $groupId)
{
    $user = auth()->user();
    $group = Group::find($groupId);
    $data = $request->all();
    $files = $data["files"];
    if (empty($files)) {
        return response()->json(['message' => 'No files provided']);
    }
    $response = ['message' => 'operation failed'];
    $allFilesFree = true;
    $unreservedFilesCount = 0; 
    foreach ($files as $file) {
        $status = MyFile::where('id', '=', $file["id"])->value('status');
        if ($status === "reserved") {
            $allFilesFree = false;
            break;
        }
        if ($status !== "reserved" && $unreservedFilesCount < 5) {
            $unreservedFilesCount++;
        }
    }
   // $countReserveFileByUser=MyFile::where('user_id', '=', $user->id)->count();

    if ($allFilesFree && $unreservedFilesCount === count($files) ) {
        foreach ($files as $file) {
            $fileModel = MyFile::find($file["id"]);
            $fileModel->status = 'reserved';
            $fileModel->user_id = 1;
            $result = $fileModel->save();

            $report = new Report;
            $report->user_name = 'test';
            $report->file_name = $fileModel->file_name;
            $report->group_name = $group->group_name;
            $report->the_operation = 'reserve file';
            $report->the_time = Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d H:i');
            $report->save();

            if ($result) {
                $file = Storage::disk('public')->path("upload/{$fileModel->file_name}");
                response()->download($file, $fileModel->file_name);
            }
        }
         return response()->json("All files reserved successfully", 200);
    } else {
        return response()->json("Some files are already reserved or you have exceeded the maximum limit of files", 400);
    }
}

 public function ReplaceFile ($id,Request $request, $groupId)
 { 
    $user_id=auth()->user()->id;
    $file = MyFile::find($id);
    if($file->user_id ==  $user_id)
    {

    $user = auth()->user();
    $group = Group::find($groupId);
    $file = $request->file('link');
    $fileName = time().$file->getClientOriginalName();
    $path = $file->storePubliclyAs('public/upload', $fileName);
    $link = Storage::url($path);
    $file= MyFile::find($id);
    $file->link = $link;
    $file->file_name = $fileName;
    $file->status = 'free';
    $file->user_id = 'null';
    $file->group_id = $group->id;
    $result=$file->save();

    $report = new Report;
    $report->user_name=auth()->user()->name;
    $report->file_name=$fileName;
    $report->group_name=$group->group_name;
    $report->the_operation='replace file';
    $report->the_time=Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d H:i');
    $result2=$report->save();

   if($result){
    return response()->json("data has been replaced", 200);
 }
}
else{
    return response()->json("you dont have permission to replace file", 500);
 }
}

 public function allReport()
 {
    $reports=Report::all();
    return response()->json($reports);
 }

 public function FileByGroup($groupId)
 {
    $group =Group::find($groupId);
    $files=$group->files()->get();
    return response()->json($files);

 }

 public function filesByReserve()
  {
    $userId=auth()->user()->id;
    $files=MyFile::where('user_id','=',$userId)->get();
    return $files;

  }

}



