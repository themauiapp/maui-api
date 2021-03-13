<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        $token = bin2hex(openssl_random_pseudo_bytes(50));
        $user = User::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
            'activation_token' => $token,
            'timezone' => $args['timezone'],
        ]);

        // Mail::to($user)->send(new VerifyEmail($user));

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
