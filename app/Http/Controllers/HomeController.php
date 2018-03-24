<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request) {
        $dev = env('APP_ENV');
        $scApp = env('SC_APP');
        $scCallback = env('SC_CALLBACK');
        $scScope = explode(',', env('SC_SCOPE'));
        $tmpScope = array_map(function ($v) {
          return "'{$v}'";
        }, $scScope);

        $userInfo = session('user');
        $accessToken = session('access_token');

        $data = [
            'title' => 'Steem Editor',
            'dev' => $dev,
            'scApp' => $scApp,
            'scCallback' => $scCallback,
            'scScope' => implode(',', $tmpScope),
            'username' => isset($userInfo['user']) ? $userInfo['user'] : null,
            'accessToken' => isset($accessToken) ? $accessToken : null,
            'userInfo' => json_encode($userInfo),
        ];
        return view('home', $data);
    }

    public function callback(Request $request) {
        $data = [
            'access_token' => $request->input('access_token'),
            'expires_in' => $request->input('expires_in'),
            'username' => $request->input('username'),
        ];
        $userInfo = $this->getUserinfoByAccessToken($data['access_token']);
        if ($userInfo['user'] === $data['username']) {
            // login success
            session(['user' => $userInfo, 'access_token' => $data['access_token']]);
        } else {
            // login failed
            session(['user' => []]);
        }
        return redirect()->action('HomeController@index');
    }

    public function logout(Request $request) {
        $accessToken = $request->input('accessToken');
        session(['access_token' => '', 'user' => '']);
        return response()->json([]);
    }

    /**
     * This is an temporary function for SteemConnect
     */
    private function getUserinfoByAccessToken($accessToken) {
        $url = 'https://steemconnect.com/api/me';
        $httpParams = [
            'headers' => [
                'Content-Type'      => 'application/json',
                'Accept'            => 'application/json, text/plain, */*',
                'Authorization'     => $accessToken,
            ]
        ];

        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $url, $httpParams);
        return json_decode($res->getBody()->getContents(), true);
    }


}
