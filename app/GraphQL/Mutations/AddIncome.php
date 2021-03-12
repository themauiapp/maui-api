<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Period;

class AddIncome
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
        $month = date('n');
        $year = date('Y');

        $period = Period::firstOrCreate([
            'month' => $month,
            'year' => $year
        ], []);

        $income = Income::create([
            'user_id' => $this->request->user()->id,
            'period_id' => $period->id,
            'total' => $args['income'],
            'remainder' => $args['income']
        ]);

        $user = $this->request->user();
        $user->total_income += $args['income'];
        $user->save();

        return [
            'message' => 'income recorded successfully',
            'income' => $income
        ];
    }   
}
