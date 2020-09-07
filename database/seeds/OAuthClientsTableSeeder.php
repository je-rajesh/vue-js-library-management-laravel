<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OAuthClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            'name' => 'Laravel Personal Access Token',
            'secret' => 'T1WQlGEEKfypwNXyG66MXxCB3nRjifLdb2Qykeq9',
            'redirect' => 'http://localhost/book',
            'personal_access_client' => 1, 
            'password_client' => 0, 
            'revoked' => 0, 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oauth_clients')->insert([
            'name' => 'Laravel Password Grant Client',
            'secret' => 'BSaQIHEdMh3koczydwoDCXdUzvvy3zHkjVclVcnd',
            'provider' => 'users',
            'redirect' => 'http://localhost/book',
            'personal_access_client' => 0, 
            'password_client' => 1, 
            'revoked' => 0, 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
