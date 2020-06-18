<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Users;
use Illuminate\Support\Facades\DB;

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

        $userSession = session('user');
        $userInfo = isset($userSession['user_info'])
                        ? $userSession['user_info'] : null;
        $accessToken = isset($userSession['access_token'])
                        ? $userSession['access_token'] : null;

        $data = [
            'title' => 'SteemEditor by ETY001',
            'dev' => $dev,
            'scApp' => $scApp,
            'scCallback' => $scCallback,
            'scScope' => implode(',', $tmpScope),
            'username' => isset($userInfo['user']) ? $userInfo['user'] : null,
            'userid' => isset($userSession['user_id']) ? $userSession['user_id'] : null,
            'accessToken' => isset($accessToken) ? $accessToken : null,
            'userInfo' => json_encode($userInfo),
            'baseUrl' => $_SERVER["HTTP_HOST"],
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
        $userSession = [
            'user_info' => $userInfo,
            'user_id' => 0,
            'access_token' => $data['access_token'],
        ];
        if ($userInfo['user'] === $data['username']) {
            $dbUser = Users::where('username', $userInfo['user'])->first();
            if ($dbUser === null) {
                $dbUser = new Users;
                $dbUser->username = $userInfo['user'];
                $dbUser->origin_data = json_encode($userInfo);
                $dbUser->token = $data['access_token'];
                $dbUser->expire = $data['expires_in'];
                $dbUser->last_login = time();
                $dbUser->created_at = time();
                $dbUser->save();
            } else {
                $dbUser->origin_data = json_encode($userInfo);
                $dbUser->token = $data['access_token'];
                $dbUser->expire = $data['expires_in'];
                $dbUser->last_login = time();
                $dbUser->save();
            }
            // login success
            $userSession['user_id'] = $dbUser->id;
            session(['user' => $userSession]);
        } else {
            // login failed
            session(['user' => []]);
        }
        return redirect()->action('HomeController@index');
    }

    public function logout(Request $request) {
        $accessToken = $request->input('accessToken');
        session(['user' => []]);
        return response()->json([]);
    }

    public function test(Request $request) {
        $users = DB::select('select * from users where username = ?', ['ety']);
        dump($users);die();
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
