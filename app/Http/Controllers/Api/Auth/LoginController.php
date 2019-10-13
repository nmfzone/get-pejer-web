<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface;
use App\Http\Controllers\Api\Controller;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Client as PassportClient;

class LoginController extends Controller
{
    /**
     * The login failed message.
     *
     * @var string
     */
    protected $loginFailedMessage;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->loginFailedMessage = trans('auth.failed');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $this->credentials($request);

        $credentials = array_merge([
            'username' => $credentials['email'],
        ], $credentials);

        return $this->access('password', $credentials);
    }

    /**
     * Handle a refresh token request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function refreshToken(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'required|string',
        ]);

        return $this->access('refresh_token', [
            'refresh_token' => $request->refresh_token,
        ]);
    }

    /**
     * Send request to the laravel passport.
     *
     * @param  string  $grantType
     * @param  array  $data
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    private function access($grantType, array $data = [])
    {
        $oauth = PassportClient::find(2);

        if (is_null($oauth)) {
            throw new Exception('Oauth not set.');
        }

        $data = array_merge([
            'client_id' => $oauth->id,
            'client_secret' => $oauth->secret,
            'grant_type' => $grantType,
        ], $data);

        $http = new Client();

        try {
            $guzzleResponse = $http->post(config('app.url') . '/oauth/token', [
                'form_params' => $data,
                'timeout' => 15,
            ]);
        } catch (BadResponseException $e) {
            $guzzleResponse = $e->getResponse();
        }

        if (! $this->validateUserState($data, $guzzleResponse)) {
            $this->sendFailedLoginResponse();
        }

        $response = response()->json(json_decode($guzzleResponse->getBody()));
        $response->setStatusCode($guzzleResponse->getStatusCode());

        $headers = $guzzleResponse->getHeaders();
        foreach ($headers as $headerType => $headerValue) {
            $response->header($headerType, $headerValue);
        }

        return $response;
    }

    /**
     * Validate the user state.
     *
     * @param  array  $data
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return bool
     */
    protected function validateUserState(array $data, ResponseInterface $response)
    {
        $response = json_decode($response->getBody());

        if (! is_null(object_get($response, 'access_token'))) {
            $request = request();

            $request->headers->add([
                'Authorization' => 'Bearer ' . $response->access_token,
            ]);

            $user = $request->user('api');

            // We can perform user state checking here. Something like user is_active state.
            if (true) {
                event(new Login('api', $user, false));

                return true;
            }

            $this->revokeAccessToken($user);
        }

        return false;
    }

    /**
     * Logout user from the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        event(new Logout('api', $user));
        $this->revokeAccessToken($user);

        return $this->response([
            'message' => trans('auth.logout'),
        ]);
    }

    /**
     * Revoke the user current access token.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    protected function revokeAccessToken(Authenticatable $user)
    {
        $accessToken = $user->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true,
            ]);

        $accessToken->revoke();
    }

    /**
     * Throw failed login error message.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse()
    {
        throw ValidationException::withMessages([
            'email' => $this->loginFailedMessage,
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }
}
