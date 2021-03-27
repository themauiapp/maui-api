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

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }   

    public function __invoke($_, array $args)
    {
        $user = $this->request->user();
        $id = $args['id'];
        $name = array_key_exists('name', $args) ? strtolower($args['name']) : NULL;
        $amount = array_key_exists('amount', $args) ? $args['amount'] : NULL;
        date_default_timezone_set($user->timezone);

        try {
            $expense = Expense::findOrFail($id);
        }
        catch(ModelNotFoundException $e) {
            return [
                'message' => 'expense does not exist',
                'errorId' => 'ExpenseDoesNotExist'
            ];
        }

        if($expense->user->id !== $user->id) {
            return [
                'message' => 'you do not have permission',
                'errorId' => 'Unauthorized'
            ];
        }

        $income = $expense->income;
        $uniqueExpense = UniqueExpense::where('user_id', $user->id)
        ->where('name', $expense->name)
        ->first();

        if($name) {
            $expenseDateStart = explode(' ', $expense->created_at, 2)[0];
            $namedExpense = Expense::where('user_id', $user->id)
            ->where('name', $name)
            ->where('created_at', '>=', $expenseDateStart.' '.'00:00:00')
            ->where('created_at', '<=', $expenseDateStart.' '.'23:59:59')
            ->where('id', '!=', $expense->id)
            ->first();

            if($namedExpense && !$amount) {
                $oldUniqueExpense = $uniqueExpense;
                if($oldUniqueExpense->total === $expense->amount) {
                    $oldUniqueExpense->delete();
                }
                else {
                    $oldUniqueExpense->total -= $expense->amount;
                    $oldUniqueExpense->save();
                }
                $uniqueExpense = UniqueExpense::where('user_id', $user->id)
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

            if($namedExpense && $amount) {
                $oldUniqueExpense = $uniqueExpense;
                if($oldUniqueExpense->total === $expense->amount) {
                    $oldUniqueExpense->delete();
                }
                else {
                    $oldUniqueExpense->total -= $expense->amount;
                    $oldUniqueExpense->save();
                }
                $uniqueExpense = UniqueExpense::where('user_id', $user->id)
                ->where('name', $namedExpense->name)
                ->first();
                $income->remainder += $expense->amount;
                $user->total_income += $expense->amount;
                $uniqueExpense->total += $amount;
                $namedExpense->amount += $amount;
                $income->remainder -= $amount;
                $user->total_income -= $amount;
                $uniqueExpense->save();
                $income->save();
                $user->save();
                $namedExpense->save();
                $expense->delete();
                return [
                    'message' => 'expense updated successfully',
                    'expense' => $namedExpense,
                ];
            }

            if(!$namedExpense && !$amount) {
                $oldUniqueExpense = $uniqueExpense;
                if($oldUniqueExpense->total === $expense->amount) {
                    $oldUniqueExpense->delete();
                }
                else {
                    $oldUniqueExpense->total -= $expense->amount;
                    $oldUniqueExpense->save();
                }
                $uniqueExpense = UniqueExpense::where('user_id', $user->id)
                ->where('name', $name)
                ->first();

                if(!$uniqueExpense) {
                    UniqueExpense::create([
                        'user_id' => $user->id,
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

            if(!$namedExpense && $amount) {
                $oldUniqueExpense = $uniqueExpense;
                if($oldUniqueExpense->total === $expense->amount) {
                    $oldUniqueExpense->delete();
                }
                else {
                    $oldUniqueExpense->total -= $expense->amount;
                    $oldUniqueExpense->save();
                }
                $uniqueExpense = UniqueExpense::where('user_id', $user->id)
                ->where('name', $name)
                ->first();

                if(!$uniqueExpense) {
                    UniqueExpense::create([
                        'user_id' => $user->id,
                        'name' => $name,
                        'total' => $amount
                    ]);
                }
                else {
                    $uniqueExpense->total += $amount;
                    $uniqueExpense->save();
                }
                $income->remainder += $expense->amount;
                $user->total_income += $expense->amount;
                $uniqueExpense->total += $expense->amount;
                $uniqueExpense->name = $name;
                $expense->name = $name;
                $expense->amount = $amount;
                $income->remainder -= $amount;
                $user->total_income -= $amount;
                $uniqueExpense->total -= $amount;
                $income->save();
                $user->save();
                $expense->save();
                $uniqueExpense->save();
                return [
                    'message' => 'expense updated successfully',
                    'expense' => $expense,
                ];
            }
        }
        else if($amount) {
            $income->remainder += $expense->amount;
            $user->total_income += $expense->amount;
            $uniqueExpense->total += $expense->amount;
            $expense->amount = $amount;
            $income->remainder -= $amount;
            $user->total_income -= $amount;
            $uniqueExpense->total -= $amount;
            $income->save();
            $user->save();
            $uniqueExpense->save();
            $expense->save();
            return [
                'message' => 'expense updated successfully',
                'expense' => $expense,
            ];
        }

        return [
            'message' => 'nothing to update',
            'expense' => $expense,
        ];
    }
}
