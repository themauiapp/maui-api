<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Income;
use App\Models\UniqueExpense;

class ExpenseStats
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
        $name = $args['name'];
        $user = $this->request->user();

        $uniqueExpense = UniqueExpense::where('user_id', $user->id)
        ->where('name', $name)
        ->first();

        if(!$uniqueExpense) {
            return [
                'message' => 'expense does not exist',
                'errorId' => 'ExpenseDoesNotExist', 
            ];
        }

        $income_total = Income::where('user_id', $user->id)
        ->sum('total');

        $income_spent = $income_total - $user->total_income;
        
        $percent_of_expenses = number_format(($uniqueExpense->total / $income_spent) * 100, 2, '.', '') . '%';

        $firstRecordedExpense = Expense::where('user_id', $user->id)
        ->where('name', $name)
        ->first();

        $lastRecordedExpense = Expense::where('user_id', $user->id)
        ->where('name', $name)
        ->orderBy('created_at', 'desc')
        ->first();

        $count = Expense::where('user_id', $user->id)
        ->where('name', $name)
        ->count();

        return [
            'name' => $name,
            'total' => number_format($uniqueExpense->total),
            'first_recorded' => $firstRecordedExpense->created_at,
            'last_recorded' => $lastRecordedExpense->created_at,
            'times_recorded' => $count,
            'percent_of_expenses' => $percent_of_expenses
        ];
    }
}
