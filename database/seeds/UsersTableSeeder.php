<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'name' => 'alex roger', 
            'email' => 'alex@e.i',
            'password' => Hash::make('password1'),
        ]);
        DB::table('users')->insert([
            'name' => 'martina higgins', 
            'email' => 'amrtina@e.i',
            'password' => Hash::make('password2'),
        ]);
        DB::table('users')->insert([
            'name' => 'golum vinci', 
            'email' => 'golun@e.i',
            'password' => Hash::make('password3'),
        ]);
    }
}
