<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Signup
{
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

    public function validateTimezone($timezone) {
        $fileName = 'timezones.json';
        $fileHandler = fopen($fileName, 'r') or die('unable to open file');
        $data = fread($fileHandler, filesize($fileName));
        $timezones = json_decode($data, true);
        fclose($fileHandler);
        return in_array($timezone, $timezones);
    }
}
