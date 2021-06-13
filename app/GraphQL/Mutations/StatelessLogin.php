<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StatelessLogin
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function __invoke($_, array $args)
    {
        $user = User::where('email', $args['email'])->first();

        if(!$user) {
            return [
                'message' => 'incorrect email or password',
                'errorId' => 'AuthenticationFailed'
            ];
        }

        if(!Hash::check($args['password'], $user->password)) {
            return [
                'message' => 'incorrect email or password',
                'errorId' => 'AuthenticationFailed'
            ];
        }

        if($user->telegram_id) {
            return [
                'message' => 'user is already authenticated to telegram',
                'errorId' => 'AuthenticatedToTelegramAlready'
            ];
        }

        $user->tokens()->delete();
        $uniqueToken = bin2hex(openssl_random_pseudo_bytes(64));
        $token = $user->createToken($uniqueToken);
        $user->telegram_id = $args['telegram_id'];
        $user->save();

        return [
            'message' => 'authenticated successfully',
            'user' => $user,
            'token' => $token->plainTextToken 
        ];
    }
}
