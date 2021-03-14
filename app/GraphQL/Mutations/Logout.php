<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Logout
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
        Auth::logout();
        $this->request->session()->invalidate();
        $this->request->session()->regenerateToken();
        return [
            'message' => 'logged out successfully'
        ];
    }
}
