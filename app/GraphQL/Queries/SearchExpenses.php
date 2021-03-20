<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Expense;

class SearchExpenses
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
        $searchTerm = $args['searchTerm'];

        $sum = Expense::where('user_id', $user->id)
        ->where('name', 'LIKE', "%{$searchTerm}%")
        ->orderBy('created_at', 'desc')
        ->sum('amount');

        if(array_key_exists('all', $args) && $args['all']) {
            $expenses = Expense::where('user_id', $user->id)
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orderBy('created_at', 'desc')
            ->get();

            return [
                'sum' => $sum,
                'expenses' => $expenses
            ];
        }

        $number = $args['number'] ?? 1;
        $page = $args['page'] ?? 1;
        $skip = ($page - 1) * $number;

        $expenses = Expense::where('user_id', $user->id)
        ->where('name', 'LIKE', "%{$searchTerm}%")
        ->orderBy('created_at', 'desc')
        ->skip($skip)
        ->take($number)
        ->get();

        $totalExpenses = Expense::where('user_id', $user->id)
        ->where('name', 'LIKE', "%{$searchTerm}%")
        ->count();

        $maxPages = ceil($totalExpenses / $number);

        return  [
            'sum' => $sum,
            'expenses' => $expenses,
            'pagination' => [
                'currentPage' => $page,
                'maxPages' => $maxPages
            ]
        ];
    }
}
