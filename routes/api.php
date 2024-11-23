<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("salam", function (){

    return response()->json([
        "status" => "success",
        "message" => "salam dari dunia",

    ]);

});


Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function (){
Route::post('/logout', [AuthController::class, 'logout']);


});