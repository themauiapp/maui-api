<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\EmailReset;
use App\Events\VerifyResetEmail as VerifyResetEmailEvent;

class VerifyResetEmail
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
        $user = $this->request->user();
        date_default_timezone_set($user->timezone);
        $password = $args['password'];

        if(Hash::check($password, $user->password)) {
            $token = bin2hex(openssl_random_pseudo_bytes(50));
            $expires_at = time() + (30 * 60);
            EmailReset::create([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => $expires_at
            ]);

            event(new VerifyResetEmailEvent($user, $token)); 
        }

        return [
            'message' => 'verify reset email sent successfully',
        ];
    }
}
