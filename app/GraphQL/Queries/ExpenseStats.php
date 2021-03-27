<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Expense;
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

        $firstRecordedExpense = Expense::where('user_id', $user->id)
        ->where('name', $name)
        ->first();

        $lastRecordedExpense = Expense::where('user_id', $user->id)
        ->where('name', $name)
        ->orderBy('created_at', 'desc')
        ->first();

        return [
            'name' => $name,
            'total' => $uniqueExpense->total,
            'first_recorded' => $firstRecordedExpense->created_at,
            'last_recorded' => $lastRecordedExpense->created_at
        ];
    }
}
