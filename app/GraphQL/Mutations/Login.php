<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Login
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    protected $request;

    public function __construct(Request $request) 
    {
        $this->request = $request;
    } 

    public function __invoke($_, array $args)
    {
        $authArgs = Arr::except($args, ['cliToken']);
        if(Auth::attempt($authArgs, true)) {
            $this->request->session()->regenerate();
            $user = User::firstWhere('email', $args['email']);
            $token = $this->generateStatelessToken($user, $args);

            return [
                'message' => 'authenticated successfully',
                'user' => $user,
                'token' => $token,
            ];
        }

        return [
            'message' => 'incorrect email or password',
            'user' => NULL,
            'errorId' => 'AuthenticationFailed'
        ];
    }

    public function generateStatelessToken(User $user, array $args)
    {
        if(!isset($args['cliToken'])) return NULL;
        $uniqueToken = bin2hex(openssl_random_pseudo_bytes(64));
        $token = $user->createToken($uniqueToken);
        Cache::put($args['cliToken'], $token->plainTextToken, now()->addMinutes(10));
        return $token->plainTextToken;
    }
}
