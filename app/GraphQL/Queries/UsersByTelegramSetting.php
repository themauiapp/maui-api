<?php

namespace App\GraphQL\Queries;

use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class UsersByTelegramSetting
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $field = sprintf("notify_%s", $args['time']);
        return User::whereHas('telegram', fn (Builder $query) => $query->where($field, 1))->get();
    }
}
