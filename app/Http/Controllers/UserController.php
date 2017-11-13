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
            'user' => 'required|integer',
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
        return response('422 Problem adding money', 422);

    }

    
    /**
     *  Withdraw money from user id.
     *
     * @param  int  $id float $amount
     * @return Response
     */
    public function withdrawMoney(Request $request)
    {      
        $validator = Validator::make($request->all(), [
            'user' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric'
        ]);
        
        Validator::extend('foo', function($attribute, $value, $parameters)
        {
            return $value == 'foo';
        });

        if ($validator->fails()) 
        {
            return response($validator->errors(), 422);
        }
        
        if (User::where([['id', $request->input('user')],[ 'balance', '>=', $request->input('amount')]])
            ->decrement('balance', $request->input('amount')))
        {
            return response('200 OK', 200);
        }
        return response('422 user don\'t have enough money', 422);
        
    }
    
    
    /**
     *  Transfer money from user id to anothe user id.
     *
     * @param  int $from int $to float $amount
     * @return Response
     */
    public function transferMoney(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'from' => 'required|integer|exists:users,id',
            'to' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) 
        {
            return response($validator->errors(), 422);
        }
        
        DB::beginTransaction();
        
        $from = User::where([['id', $request->input('from')],[ 'balance', '>=', $request->input('amount')]])
          ->decrement('balance', $request->input('amount'));

        $to = User::where(['id' => $request->input('to')])
          ->increment('balance', $request->input('amount'));
        
        if( !$from || !$to )
        {
            DB::rollback();
            return response('User from don\'t have enough money for transferring', 422);
        } else {
            // Else commit the queries
            DB::commit();
            return response('', 200);
        }
    }    
}