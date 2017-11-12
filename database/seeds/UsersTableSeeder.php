<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 101,  
            'balance' => '100',
        ]);
        
        DB::table('users')->insert([
            'id' => 205,  
            'balance' => '0',
        ]);
      
    }
}
