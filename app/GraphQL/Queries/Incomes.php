<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Income;
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
        $user = $this->request->user();
        $page = $args['page'];
        $number = $args['number'];
        $skip = ($page - 1) * $number;
        $totalIncomes = Income::where('user_id', $user->id)->count();
        $incomes = Income::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->skip($skip)
        ->take($number)
        ->get();
        $maxPages = ceil($totalIncomes / $number);

        return [
            'incomes' => $incomes,
            'pagination' => [
                'currentPage' => $page,
                'maxPages' => $maxPages
            ]
        ];
    }
}
