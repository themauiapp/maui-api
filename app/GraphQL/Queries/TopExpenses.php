<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\UniqueExpense;

class TopExpenses
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
        $expenses = UniqueExpense::where('user_id', $user->id)
        ->orderBy('total', 'desc')
        ->select('name', 'total')
        ->take(5)
        ->get();

        return [
            'expenses' => $expenses
        ];
    }
}
