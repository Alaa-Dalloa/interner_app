<?php

namespace Tests\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use Tests\Feature\UserTasksTest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
 
    'middleware' => 'api',
    'prefix' => 'auth'
 
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/allUser', [AuthController::class, 'index']);
    Route::get('/allUser/{id}', [GroupController::class, 'index']);

 //Files
    
   Route::post('uploadFile/{groupId}', [FileController::class, 'uploadFile']);
   Route::post('reserveFile/{id}', [FileController::class, 'reserveFile']);
   Route::post('reserveFiles/{groupId}', [FileController::class, 'reserveFiles']);
   Route::get('indexFiles', [FileController::class, 'indexFiles']);
   Route::post('FileByGroup/{groupId}', [FileController::class, 'FileByGroup']);
   Route::post('/{id}/ReplaceFile/{groupId}', [FileController::class, 'ReplaceFile']);
   Route::get('indexFilesRESERVE', [FileController::class, 'filesByReserve']);


 //Add Group
   
   Route::post('AddGroup', [GroupController::class, 'AddGroup']);
   Route::get('indexGroup', [GroupController::class, 'indexGroup']);
   Route::post('/{groupId}/add-user/{userId}', [GroupController::class, 'addUserToGroup']);
   Route::post('/{groupId}/delete-user/{userId}', [GroupController::class, 'removeUserFromGroup']);
   Route::post('/Deletegroup/{groupId}', [GroupController::class, 'destroy']);
   Route::get('GroupByPer', [GroupController::class, 'GroupByPer']);

 // Report
 Route::get('allReport', [FileController::class, 'allReport']);

   

});
