<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class PassportService
{
    public function getTokens($username, $password)
    {
        $url = env("APP_URL") . "/oauth/token";
        $clientSecret = DB::table('oauth_clients')->where('id', env('CLIENT_ID'))->select('secret')->first();
        $http = new Client;
        try {
            $response = $http->post($url, [
                'form_params' => [
                    'grant_type' => env('GRANT_TYPE'),
                    'client_id'  => env('CLIENT_ID'),
                    'client_secret' => $clientSecret ? $clientSecret->secret : env('CLIENT_SECRET'),
                    'username' => $username,
                    'password' => $password,
                    'scope'    => '*',
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception  $exception) {
            if ($exception->getCode() === 400) {
                return response_fail('Invalid Request. Please enter a username or a password',$exception->getCode());
            } elseif ($exception->getCode() === 401) {
                return response_fail('Your credentials are incorrect. Please try again',$exception->getCode());
            }
            return response_fail(['message' => $exception->getMessage()], $exception->getCode());
        }
    }
}
