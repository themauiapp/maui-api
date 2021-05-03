<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Expense;

class DailyExpenses
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
        $date = $args['date'];

        $sum = Expense::where('user_id', $user->id)
        ->where('created_at', '>=', $date.' '.'00:00:00')
        ->where('created_at', '<=', $date.' '.'23:59:59')
        ->sum('amount');

        if(array_key_exists('all', $args) &&  $args['all']) {
            $expenses = Expense::where('user_id', $user->id)
            ->where('created_at', '>=', $date.' '.'00:00:00')
            ->where('created_at', '<=', $date.' '.'23:59:59')
            ->orderBy('created_at', 'asc')
            ->get();

            return [
                'expenses' => $expenses,
                'sum' => $sum
            ];
        }

        $number = $args['number'] ?? 1;
        $page = $args['page'] ?? 1;
        $skip = ($page - 1) * $number;

        $expenses = Expense::where('user_id', $user->id)
        ->where('created_at', '>=', $date.' '.'00:00:00')
        ->where('created_at', '<=', $date.' '.'23:59:59')
        ->orderBy('created_at', 'asc')
        ->skip($skip)
        ->take($number)
        ->get();

        $totalExpenses = Expense::where('user_id', $user->id)
        ->where('created_at', '>=', $date.' '.'00:00:00')
        ->where('created_at', '<=', $date.' '.'23:59:59')
        ->count();

        $maxPages = ceil($totalExpenses / $number);

        return [
            'expenses' => $expenses,
            'sum' => number_format($sum),
            'pagination' => [
                'currentPage' => $page,
                'maxPages' => $maxPages
            ]
        ];
    }
}
