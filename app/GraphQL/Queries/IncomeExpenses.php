<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Period;

class IncomeExpenses
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
        $date = strtotime($args['date']);
        $month = date("m", $date);
        $year = date("y", $date);

        $period = Period::where('month', $month)
        ->where('year', $year)
        ->first();

        if(!$period) {
            return [
                'expenses' => [],
                'sum' => 0,
                'pagination' => [
                    'currentPage' => 0,
                    'maxPages' => 0
                ]
            ];
        }

        $income = Income::where('user_id', $user->id)
        ->where('period_id', $period->id)
        ->first();

        if(!$income) {
            return [
                'expenses' => [],
                'sum' => 0,
                'pagination' => [
                    'currentPage' => 0,
                    'maxPages' => 0
                ]
            ];
        }

        $sum = Expense::where('income_id', $income->id)
        ->sum('amount');

        if(array_key_exists('all', $args) && $args['all']) {
            $expenses = Expense::where('income_id', $income->id)
            ->orderBy('created_at', 'desc')
            ->get();

            return [
                'expenses' => $expenses,
                'sum' => $sum
            ];
        }

        $page = $args['page'] ?? 1;
        $number = $args['number'] ?? 1;
        $skip = ($page - 1) * $number;

        $expenses = Expense::where('income_id', $income->id)
        ->orderBy('created_at', 'desc')
        ->skip($skip)
        ->take($number)
        ->get();

        $totalExpenses = Expense::where('income_id', $income->id)
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
