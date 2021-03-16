<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;

class Incomes
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
        return [
            'incomes' => $this->request->user()->incomes,
        ];
    }
}
