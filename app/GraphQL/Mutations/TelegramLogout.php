<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;

class TelegramLogout
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
        $user->currentAccessToken()->delete();
        $user->telegram_id = NULL;
        $user->save();
        return [
            'message' => 'logged out successfully'
        ];
    }
}
