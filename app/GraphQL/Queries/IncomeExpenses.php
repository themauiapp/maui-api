<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Income;
use App\Models\Expense;

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
        $income_id = $args['income_id'];

        try {
            $income = Income::findOrFail($income_id);
        }
        catch(ModelNotFoundException $e) {
            return [
                'message' => 'income does not exist',
                'errorId' => 'IncomeDoesNotExist'
            ];
        }

        if($income->user->id !== $user->id) {
            return [
                'message' => 'you do not have permission',
                'errorId' => 'Unauthorized'
            ];
        }

        $sum = Expense::where('income_id', $income_id)
        ->sum('amount');

        if(array_key_exists('all', $args) && $args['all']) {
            $expenses = Expense::where('income_id', $income_id)
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

        $expenses = Expense::where('income_id', $income_id)
        ->orderBy('created_at', 'desc')
        ->skip($skip)
        ->take($number)
        ->get();

        $totalExpenses = Expense::where('income_id', $income_id)
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
