<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Traits\ValidateTimezone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Signup
{
    use ValidateTimezone;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        if(!$this->validateTimezone($args['timezone'])) {
            return [
                'message' => 'timezone is not valid',
                'errorId' => 'InvalidTimezone'
            ];
        }

        $user = User::firstWhere('email', $args['email']);

        if($user) {
            return [
                'message' => 'user with email exists',
                'errorId' => 'UserExistsAlready'
            ];
        }

        $user = User::create([
            'first_name' => $args['first_name'],
            'last_name' => $args['last_name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
            'timezone' => $args['timezone'],
        ]);

        Auth::login($user, true);

        event(new Registered($user));

        return [
            'message' => 'user created successfully',
            'user' => $user
        ];
    }
}
