<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\UniqueExpense;

class Stats
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
        $income_total = Income::where('user_id', $user->id)
        ->sum('total');
        
        $income_spent = $income_total - $user->total_income;
        $top_expenses = UniqueExpense::where('user_id', $user->id)
        ->orderBy('total', 'desc')
        ->take(3)
        ->get();
        
        $top_expense_items = [];
        $top_expense_amounts = [];

        foreach($top_expenses as $expense) {
            array_push($top_expense_items, $expense->name);
            array_push($top_expense_amounts, $expense->total);
        }

        return [
            'income_total' => $income_total,
            'income_spent' => $income_spent,
            'income_remainder' => $user->total_income,
            'top_expense_items' => $top_expense_items,
            'top_expense_amounts' => $top_expense_amounts,
        ];
    }
}
