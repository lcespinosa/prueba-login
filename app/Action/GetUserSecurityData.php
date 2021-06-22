<?php
namespace App\Action;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class GetUserSecurityData
{
    /**
     * @var User $user
     * */
    private $user;

    public function execute()
    {
        $data = $this->getTokenAndRefreshToken();

        return [
            'user'          => $this->user->toArray(),
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_type'    => $data['token_type'],
            'expires_in'    => $data['expires_in'],
            'expires_at'    => now()->addSeconds($data['expires_in'])->toDateTimeString()
        ];
    }

    private function getTokenAndRefreshToken()
    {
        return $this->requestOClient([
            'form_params' => [
                'grant_type'    => 'password',
                'username'      => $this->user->email,
                'password'      => optional(request())->get('password'),
            ]
        ]);
    }

    public function getNewToken($refreshToken)
    {
        $data = $this->requestOClient([
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]
        ]);

        return [
            'message'       => 'new_token',
            'access_token'  => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_type'    => $data['token_type'],
            'expires_in'    => $data['expires_in'],
            'expires_at'    => now()->addSeconds($data['expires_in'])->toDateTimeString()
        ];
    }

    private function requestOClient($data)
    {
        $oClient = DB::table('oauth_clients')
            ->select(['id', 'secret'])
            ->where('password_client', true)
            ->first();
        if ($oClient) {
            $client = new Client();
            $url = env('APP_URL') . '/oauth/token';
            $data['form_params']['client_id'] = $oClient->id;
            $data['form_params']['client_secret'] = $oClient->secret;
            $response = $client->post($url, $data);
            return json_decode((string) $response->getBody(), true);
        }
        return [];
    }

    public function withUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
