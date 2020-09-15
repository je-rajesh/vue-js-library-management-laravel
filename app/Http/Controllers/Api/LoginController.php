<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function GuzzleHttp\json_encode;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client();

        $url = config('app.home_index') . 'oauth/token';
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
            if ($e->getCode() === 400) {
                return response()->json('invalid Request, Please enter a valid username and password', $e->getCode());
            } else if ($e->getCode() === 401) {
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
        auth('api')->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged Out Successfully', 200);
    }

    /**
     * Otp based authentication for laravel. 
     */

    public function otp_login(Request $request)
    {
        $http = new \GuzzleHttp\Client();

        $url = config('app.home_index') . 'oauth/token';
        // die($url);  

        try {
            $response = $http->post($url,  [
                'form_params' => [
                    'grant_type' => 'otp_grant',
                    'client_id' => config('app.client_id'),
                    'client_secret' => config('app.client_secret'),
                    'phone_no' => $request->phone_no,
                    'otp' => $request->otp,
                ]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json((string)$e->getMessage(), $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json($e->getMessage(), $e->getCode());
            }

            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function generate_otp(Request $request)
    {
        $data = $request->validate([
            'phone_no' => 'required|min:10|max:10|digits:10',
        ]);
        $otp = rand(100000, 999999);

        Otp::updateOrCreate([
            'phone_no' => $request->phone_no,
            'otp' => $otp,
        ]);

        return response()->json(['message' => 'otp generated successfully', 'otp' => $otp], 200);
    }
}
