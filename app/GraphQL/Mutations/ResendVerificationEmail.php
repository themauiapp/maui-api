<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;

class ResendVerificationEmail
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
        $this->request->user()->sendEmailVerificationNotification();
        return [
            'message' => 'verification email resent'
        ];
    }
}
