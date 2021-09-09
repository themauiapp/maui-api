<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;

class UpdateTelegramSettings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $user = $this->request->user();
        $telegram = $user->telegram;
        $telegram->fill($args);
        $telegram->save();

        return [
            'message' => 'Telegram Settings Updated Successfully'
        ];
    }
}
