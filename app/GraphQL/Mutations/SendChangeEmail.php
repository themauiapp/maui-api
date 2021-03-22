<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use App\Events\ChangeEmail;
use App\Models\EmailReset;
use App\Models\User;

class SendChangeEmail
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
        $token = $args['token'];
        $email = $args['email'];

        $userWithEmail = User::where('email', $email)->first();

        if($userWithEmail) {
            return [
                'message' => 'user with email exists already',
                'errorId' => 'InvalidEmailAddress'
            ];
        }

        $reset = EmailReset::where('user_id', $user->id)
        ->where('token', $token)
        ->first();

        if(!$reset) {
            $reset->delete();
            return [
                'message' => 'invalid email reset token',
                'errorId' => 'InvalidResetToken'
            ];
        }

        if(time() > $reset->expires_at) {
            $reset->delete();
            return [
                'message' => 'expired email reset token',
                'errorId' => 'ExpiredResetToken'
            ];
        }

        $reset->expires_at = time() + (30 * 60);
        $reset->save();
        $user->email = $email;
        event(new ChangeEmail($user, $token));

        return [
            'message' => 'change email mail has been sent successfully'
        ];
    }
}
