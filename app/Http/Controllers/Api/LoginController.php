<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client ;

        $url = config('app.home_index').'oauth/token';
        // die($url);  

        try {
            $response = $http->post($url,  [ 
                'form_params' => [
                    'grant_type' => config('app.grant_type'),
                    'client_id' => config('app.client_id'),
                    'client_secret' => config('app.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                ]
            ]);
            
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400){
                return response()->json('invalid Request, Please enter a valid username and password', $e->getCode());
            } else if($e->getCode() === 401){
                return response()->json('Your credentials are incorrect', $e->getCode());
            }

            return response()->json('Something went wrong on the server', $e->getCode());
        }
    }

    public function register()
    {
        
    }

    public function logout()
    {
        auth('api')->user()->tokens->each(function($token, $key) {
            $token->delete();
        }) ;

        return response()->json('Logged Out Successfully', 200);
    }
}
