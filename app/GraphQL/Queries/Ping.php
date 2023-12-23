<?php

namespace App\GraphQL\Queries;

class Ping
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return [
            'status' => 'ok'
        ];
    }
}
