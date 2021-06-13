<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login
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
        if(Auth::attempt($args, true)) {
            $this->request->session()->regenerate();
            $user = User::firstWhere('email', $args['email']);
            return [
                'message' => 'authenticated successfully',
                'user' => $user
            ];
        }

        return [
            'message' => 'incorrect email or password',
            'user' => NULL,
            'errorId' => 'AuthenticationFailed'
        ];
    }
}
