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
        $user = $this->request->user();
        date_default_timezone_set($user->timezone);
        $startDate = $args['date'];
        $endDate = strtotime($startDate) + 604800;
        $endDate = date('Y-m-d', $endDate);

        $sum = Expense::where('user_id', $user->id)
        ->where('created_at', '>=', $startDate.' '.'00:00:00')
        ->where('created_at', '<=', $endDate.' '.'23:59:59')
        ->sum('amount');

        if(array_key_exists('all', $args) && $args['all']) {
            $expenses = Expense::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate.' '.'00:00:00')
            ->where('created_at', '<=', $endDate.' '.'23:59:59')
            ->orderBy('created_at', 'desc')
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
