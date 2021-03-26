<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Traits\ValidateTimezone;
use App\Models\GoogleUser;
use App\Models\User;
use \Exception;

class VerifyGoogleLogin
{
    use ValidateTimezone;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $timezone = $args['timezone'];

        if(!$this->validateTimezone($timezone)) {
            return [
                'message' => 'timezone is not valid',
                'errorId' => 'InvalidTimezone'
            ];
        }

        try {
            $providerUser = Socialite::driver('google')->stateless()->user();
        }
        catch(Exception $e) {
            return [
                'message' => 'an error occured'
            ];
        }

        $googleUser = GoogleUser::firstWhere('google_id', $providerUser->getId());

        if(!$googleUser) {
            $user = User::firstWhere('email', $providerUser->getEmail());

            if(!$user) {
                date_default_timezone_set($timezone);
                $password = bin2hex(openssl_random_pseudo_bytes(50));
                $names = explode(' ', $providerUser->getName(), 2);
                $first_name = $names[0];
                $last_name = $names[1] ?? '';
                $user = User::create([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $providerUser->getEmail(),
                    'email_verified_at' => date('Y-m-d h:i:s'),
                    'password' => Hash::make($password),
                    'avatar' => $providerUser->getAvatar(),
                    'timezone' => $timezone
                ]);
            }

            GoogleUser::create([
                'google_id' => $providerUser->getId(),
                'user_id' => $user->id
            ]);

            Auth::login($user);

            return [
                'message' => 'authenticated successfully',
                'user' => $user
            ];
        }

        $user = $googleUser->user;
        Auth::login($user);

        return [
            'message' => 'authenticated successfully',
            'user' => $user
        ];
    }
}
