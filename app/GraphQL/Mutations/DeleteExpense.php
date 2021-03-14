<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteExpense
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
        $id = $args['id'];

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
        $income->remainder += $expense->amount;
        $user->total_income += $expense->amount;
        $income->save();
        $user->save();
        $expense->delete();

        return [
            'message' => 'expense deleted successfully'
        ];
    }
}
