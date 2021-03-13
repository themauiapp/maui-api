<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;

use App\Models\Expense;
use App\Models\Period;
use App\Models\Income;

class AddExpense
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
        date_default_timezone_set('Africa/Lagos');

        $month = date('n');
        $year = date('Y');
        $user = $this->request->user();
        $user_id = $user->id;
        $name = strtolower($args['name']);
        $amount = $args['amount'];

        $period = Period::firstOrCreate([
            'month' => $month,
            'year' => $year
        ], []);

        $income = Income::where('user_id', $user_id)
        ->where('period_id', $period->id)
        ->first();

        if(!$income) {
            return [
                'message' => 'no income exists for current period',
                'errorId' => 'NoPeriodIncomeExists'
            ];
        }

        $currentDate = date('Y-m-d').' '.'00:00:00';
        
        $expense = Expense::where('user_id', $user_id)
        ->where('income_id', $income->id)
        ->where('name', $name)
        ->where('created_at', '>=', $currentDate)
        ->first();

        if($expense) {
            $income->remainder += $expense->amount;
            $user->total_income += $expense->amount;
            $expense->amount += $amount;
            $expense->save();
            $income->remainder -= $expense->amount;
            $user->total_income -= $expense->amount;
            $income->save();
            $user->save();

            return [
                'message' => 'expense recorded successfully',
                'expense' => $expense
            ];
        }

        $expense = Expense::create([
            'user_id' => $user_id,
            'income_id' => $income->id,
            'name' => $name,
            'amount' => $amount
        ]);

        $income->remainder -= $amount;
        $income->save();
        $user->total_income -= $amount;
        $user->save();

        return [
            'message' => 'expense recorded successfully',
            'expense' => $expense
        ];
    }
}
