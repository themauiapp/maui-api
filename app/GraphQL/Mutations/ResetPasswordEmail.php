<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Password;
class ResetPasswordEmail
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Password::sendResetLink($args);

        return [
            'message' => 'password reset email sent successfully',
        ];
    }
}
