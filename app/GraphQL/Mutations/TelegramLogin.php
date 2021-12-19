<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TelegramLogin
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

        if(!$user->telegram) {
            $user->telegram()->create([
                'telegram_id' => $args['telegram_id']
            ]);
        }

        $user->tokens()->delete();
        $uniqueToken = bin2hex(openssl_random_pseudo_bytes(64));
        $token = $user->createToken($uniqueToken);

        return [
            'message' => 'authenticated successfully',
            'user' => $user,
            'token' => $token->plainTextToken 
        ];
    }
}
