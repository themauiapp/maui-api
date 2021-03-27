<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;

use App\Models\Expense;
use App\Models\UniqueExpense;
use App\Models\Period;
use App\Models\Income;

class AddExpense
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    protected $user;

    public function __construct(Request $request)
    {
        $this->user = $request->user();
    }


    public function __invoke($_, array $args)
    {
        date_default_timezone_set($this->user->timezone);

        $month = date('n');
        $year = date('Y');
        $name = strtolower($args['name']);
        $amount = $args['amount'];

        $period = Period::firstOrCreate([
            'month' => $month,
            'year' => $year
        ], []);

        if(array_key_exists('date', $args)) {
            return $this->addToDate($name, $amount, $args['date']);
        }

        $income = Income::where('user_id', $this->user->id)
        ->where('period_id', $period->id)
        ->first();

        if(!$income) {
            return [
                'message' => 'no income exists for current period',
                'errorId' => 'NoPeriodIncomeExists'
            ];
        }

        $currentDate = date('Y-m-d').' '.'00:00:00';
        
        $expense = Expense::where('user_id', $this->user->id)
        ->where('income_id', $income->id)
        ->where('name', $name)
        ->where('created_at', '>=', $currentDate)
        ->first();

        if($expense) {
            $income->remainder += $expense->amount;
            $this->user->total_income += $expense->amount;
            $expense->amount += $amount;
            $expense->save();
            $income->remainder -= $expense->amount;
            $this->user->total_income -= $expense->amount;
            $income->save();
            $this->user->save();

            $this->recordUniqueExpense($name, $amount);

            return [
                'message' => 'expense recorded successfully',
                'expense' => $expense
            ];
        }

        $expense = Expense::create([
            'user_id' => $this->user->id,
            'income_id' => $income->id,
            'name' => $name,
            'amount' => $amount
        ]);

        $income->remainder -= $amount;
        $income->save();
        $this->user->total_income -= $amount;
        $this->user->save();

        $this->recordUniqueExpense($name, $amount);

        return [
            'message' => 'expense recorded successfully',
            'expense' => $expense
        ];
    }

    public function addToDate($name, $amount, $date)
    {
        $date = strtotime($date);
        $month = date('n', $date);
        $year = date('Y', $date);

        $period = Period::where('month', $month)
        ->where('year', $year)
        ->first();

        if(!$period) {
            return [
                'message' => 'period does not exist',
                'errorId' => 'PeriodDoesNotExist'
            ];
        }

        if(date('d', $date) > date('d')) {
            return [
                'message' => 'cannot add income for days ahead',
                'errorId' => 'InvalidDate'
            ];   
        }

        $income = Income::where('period_id', $period->id)
        ->where('user_id', $this->user->id)
        ->first();

        if(!$income) {
            return [
                'message' => 'no income exists for period',
                'errorId' => 'NoPeriodIncomeExists'
            ];
        }

        $expense = Expense::where('user_id', $this->user->id)
        ->where('name', $name)
        ->where('created_at', '>=', date('Y-m-d', $date).' '.'00:00:00')
        ->where('created_at', '<=', date('Y-m-d', $date).' '.'23:59:59')
        ->first();

        if($expense) {
            $income->remainder += $expense->amount;
            $this->user->total_income += $expense->amount;
            $expense->amount += $amount;
            $expense->save();
            $income->remainder -= $expense->amount;
            $this->user->total_income -= $expense->amount;
            $income->save();
            $this->user->save();

            $this->recordUniqueExpense($name, $amount);

            return [
                'message' => 'expense recorded successfully',
                'expense' => $expense
            ];
        }

        $expense = Expense::create([
            'user_id' => $this->user->id,
            'income_id' => $income->id,
            'name' => $name,
            'amount' => $amount,
            'created_at' => date('Y-m-d', $date).' '.'23:59:59',
            'updated_at' => date('Y-m-d', $date).' '.'23:59:59'
        ]);

        $income->remainder -= $amount;
        $income->save();
        $this->user->total_income -= $amount;
        $this->user->save();

        $this->recordUniqueExpense($name, $amount);

        return [
            'message' => 'expense recorded successfully',
            'expense' => $expense
        ];
    }

    public function recordUniqueExpense($name, $amount)
    {
        $uniqueExpense = UniqueExpense::where('user_id', $this->user->id)
        ->where('name', $name)
        ->first();

        if(!$uniqueExpense) {
            $uniqueExpense = UniqueExpense::create([
                'user_id' => $this->user->id,
                'name' => $name,
                'total' => $amount
            ]);
        }
        else {
            $uniqueExpense->total += $amount;
            $uniqueExpense->save();
        }
    }
}
