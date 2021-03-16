<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Expense;
use DateTime;
use PhpParser\Node\Expr\New_;

class WeeklyExpenses
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
        $number = $args['number'];
        $page = $args['page'];
        $startDate = $args['date'];
        $skip = ($page - 1) * $number;
        $user = $this->request->user();

        date_default_timezone_set($user->timezone);

        $endDate = strtotime($startDate) + 604800;
        $endDate = date('Y-m-d', $endDate);

        $sum = Expense::where('user_id', $user->id)
        ->where('created_at', '>=', $startDate.' '.'00:00:00')
        ->where('created_at', '<=', $endDate.' '.'23:59:59')
        ->sum('amount');

        $expenses = Expense::where('user_id', $user->id)
        ->where('created_at', '>=', $startDate.' '.'00:00:00')
        ->where('created_at', '<=', $endDate.' '.'23:59:59')
        ->orderBy('created_at', 'desc')
        ->skip($skip)
        ->take($number)
        ->get();

        $totalExpenses = Expense::where('user_id', $user->id)
        ->where('created_at', '>=', $startDate.' '.'00:00:00')
        ->where('created_at', '<=', $endDate.' '.'23:59:59')
        ->count();

        $maxPages = ceil($totalExpenses / $number);

        return [
            'expenses' => $expenses,
            'sum' => $sum,
            'pagination' => [
                'currentPage' => $page,
                'maxPages' => $maxPages
            ]
        ];
    }
}
