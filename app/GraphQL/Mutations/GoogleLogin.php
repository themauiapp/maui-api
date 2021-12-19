<?php

namespace App\GraphQL\Mutations;

use Laravel\Socialite\Facades\Socialite;

class GoogleLogin
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return [
            'redirect_url' => $url
        ];
    }
}
