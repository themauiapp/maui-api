<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\UniqueExpense;

class IncomeStats
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

        return [
            'income_total' => number_format($income_total),
            'income_spent' => number_format($income_spent),
            'income_remainder' => number_format($user->total_income),
        ];
    }
}
