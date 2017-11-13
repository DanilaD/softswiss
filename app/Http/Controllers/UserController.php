<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Show the balance for the given user id.
     *
     * @param  int  $id
     * @return Response
     */
    public function showBalance(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'user' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }
    
        return response(User::select('balance')
            ->where('id', $request->input('user'))
            ->first(), 
            200);
        
    }
    
        
    /**
     * Add money to user id.
     *
     * @param  int  $id int $amount
     * @return Response
     */
    public function addMoney(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'user' => 'required|integer',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) 
        {
            return response($validator->errors(), 422);
        }
  
        if (User::updateOrCreate(
              ['id' => $request->input('user')])
              ->increment('balance', $request->input('amount'), ['id' => $request->input('user')]))              
        {
            return response('', 200);
        }        
    }
    
}