<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;
use DB;

class UserController extends Controller
{
    /**
     * Show the balance for the given user id.
     *
     * @param  int $id
     * @return Response
     */
    public function showBalance(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'user' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }
        
        $user = User::select('balance')
            ->where('id', $request->input('user'))
            ->first();

        return response('200 OK<br/>' . $user, 200);
        
    }
    
        
    /**
     * Add money to user id.
     *
     * @param  int $id float $amount
     * @return Response
     */
    public function addMoney(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'user' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) 
        {
            return response($validator->errors(), 422);
        }
  
        if (User::updateOrCreate(
            ['id' => $request->input('user')])
            ->increment('balance', $request->input('amount'), 
            ['id' => $request->input('user')]))              
        {
            return response('200 OK', 200);
        }
        return response('422 Problem to create row', 422);

    }    
}