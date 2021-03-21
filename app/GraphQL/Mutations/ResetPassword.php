<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ResetPassword
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function __invoke($_, array $args)
    {
        $status = Password::reset($args, function($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ]);

            $user->save();
        });

        return [
            'message' => 'Password reset successfully',
        ];
    }
}
