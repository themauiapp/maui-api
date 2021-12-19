<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Expense;
use App\Models\UniqueExpense;
class UpdateExpense
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    protected $user, $income;

    public function __construct(Request $request) {
        $this->user = $request->user();
    }   

    public function __invoke($_, array $args)
    {
        $id = $args['id'];
        $name = array_key_exists('name', $args) ? strtolower($args['name']) : NULL;
        $amount = array_key_exists('amount', $args) ? $args['amount'] : NULL;
        date_default_timezone_set($this->user->timezone);

        try {
            $expense = Expense::findOrFail($id);
        }
        catch(ModelNotFoundException $e) {
            return [
                'message' => 'expense does not exist',
                'errorId' => 'ExpenseDoesNotExist'
            ];
        }

        if($expense->user->id !== $this->user->id) {
            return [
                'message' => 'you do not have permission',
                'errorId' => 'Unauthorized'
            ];
        }

        $this->income = $expense->income;
        $uniqueExpense = UniqueExpense::where('user_id', $this->user->id)
        ->where('name', $expense->name)
        ->first();

        if($name) {
            $expenseDateStart = explode(' ', $expense->created_at, 2)[0];
            $namedExpense = Expense::where('user_id', $this->user->id)
            ->where('name', $name)
            ->where('created_at', '>=', $expenseDateStart.' '.'00:00:00')
            ->where('created_at', '<=', $expenseDateStart.' '.'23:59:59')
            ->where('id', '!=', $expense->id)
            ->first();

            if($namedExpense && !$amount) {
                return $this->updateNamedExpense($expense, $namedExpense, $uniqueExpense);
            }

            if($namedExpense && $amount) {
                return $this->updateNamedExpenseAndAmount($expense, $namedExpense, $uniqueExpense, $amount);   
            }

            if(!$namedExpense && !$amount) {
                return $this->updateExpenseName($expense, $uniqueExpense, $name);
            }

            if(!$namedExpense && $amount) {
                return $this->updateExpenseNameAndAmount($expense, $uniqueExpense, $name, $amount);
            }
        }
        else if($amount) {
            return $this->updateExpenseAmount($expense, $uniqueExpense, $amount);
        }

        return [
            'message' => 'nothing to update',
            'expense' => $expense,
        ];
    }

    public function updateNamedExpense($expense, $namedExpense, $uniqueExpense) 
    {
        $oldUniqueExpense = $uniqueExpense;
        
        if($oldUniqueExpense->total === $expense->amount) {
            $oldUniqueExpense->delete();
        }
        else {
            $oldUniqueExpense->total -= $expense->amount;
            $oldUniqueExpense->save();
        }

        $uniqueExpense = UniqueExpense::where('user_id', $this->user->id)
        ->where('name', $namedExpense->name)
        ->first();

        $namedExpense->amount += $expense->amount;
        $uniqueExpense->total += $expense->amount;
        $namedExpense->save();
        $uniqueExpense->save();
        $expense->delete();
        
        return [
            'message' => 'expense updated successfully',
            'expense' => $namedExpense,
        ];
    }

    public function updateNamedExpenseAndAmount($expense, $namedExpense, $uniqueExpense, $amount)
    {
        $oldUniqueExpense = $uniqueExpense;
        
        if($oldUniqueExpense->total === $expense->amount) {
            $oldUniqueExpense->delete();
        }
        else {
            $oldUniqueExpense->total -= $expense->amount;
            $oldUniqueExpense->save();
        }

        $uniqueExpense = UniqueExpense::where('user_id', $this->user->id)
        ->where('name', $namedExpense->name)
        ->first();

        $this->income->remainder += $expense->amount;
        $this->user->total_income += $expense->amount;
        $uniqueExpense->total += $amount;
        $namedExpense->amount += $amount;
        $this->income->remainder -= $amount;
        $this->user->total_income -= $amount;
        $uniqueExpense->save();
        $this->income->save();
        $this->user->save();
        $namedExpense->save();
        $expense->delete();
        
        return [
            'message' => 'expense updated successfully',
            'expense' => $namedExpense,
        ];
    }

    public function updateExpenseName($expense, $uniqueExpense, $name)
    {
        $oldUniqueExpense = $uniqueExpense;
        
        if($oldUniqueExpense->total === $expense->amount) {
            $oldUniqueExpense->delete();
        }
        else {
            $oldUniqueExpense->total -= $expense->amount;
            $oldUniqueExpense->save();
        }

        $uniqueExpense = UniqueExpense::where('user_id', $this->user->id)
        ->where('name', $name)
        ->first();

        if(!$uniqueExpense) {
            UniqueExpense::create([
                'user_id' => $this->user->id,
                'name' => $name,
                'total' => $expense->amount
            ]);
        }
        else {
            $uniqueExpense->total += $expense->amount;
            $uniqueExpense->save();
        }

        $expense->name = $name;
        $expense->save();
        
        return [
            'message' => 'expense updated successfully',
            'expense' => $expense,
        ];
    }

    public function updateExpenseNameAndAmount($expense, $uniqueExpense, $name, $amount)
    {
        $oldUniqueExpense = $uniqueExpense;
        
        if($oldUniqueExpense->total === $expense->amount) {
            $oldUniqueExpense->delete();
        }
        else {
            $oldUniqueExpense->total -= $expense->amount;
            $oldUniqueExpense->save();
        }

        $uniqueExpense = UniqueExpense::where('user_id', $this->user->id)
        ->where('name', $name)
        ->first();

        if(!$uniqueExpense) {
            UniqueExpense::create([
                'user_id' => $this->user->id,
                'name' => $name,
                'total' => $amount
            ]);
        }
        else {
            $uniqueExpense->total += $amount;
            $uniqueExpense->save();
        }

        $this->income->remainder += $expense->amount;
        $this->user->total_income += $expense->amount;
        $expense->name = $name;
        $expense->amount = $amount;
        $this->income->remainder -= $amount;
        $this->user->total_income -= $amount;
        $this->income->save();
        $this->user->save();
        $expense->save();
        
        return [
            'message' => 'expense updated successfully',
            'expense' => $expense,
        ];
    }

    public function updateExpenseAmount($expense, $uniqueExpense, $amount)
    {
        $this->income->remainder += $expense->amount;
        $this->user->total_income += $expense->amount;
        $uniqueExpense->total += $expense->amount;
        $expense->amount = $amount;
        $this->income->remainder -= $amount;
        $this->user->total_income -= $amount;
        $uniqueExpense->total -= $amount;
        $this->income->save();
        $this->user->save();
        $uniqueExpense->save();
        $expense->save();
        
        return [
            'message' => 'expense updated successfully',
            'expense' => $expense,
        ];
    }
}
