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
        $user = $this->request->user();
        date_default_timezone_set($user->timezone);
        $month = date('n');
        $year = date('Y');

        if(array_key_exists('currency', $args)) {
            $user->currency = $args['currency'];
        }

        $period = Period::firstOrCreate([
            'month' => $month,
            'year' => $year
        ], []);

        $income = Income::where('user_id', $user->id)
        ->where('period_id', $period->id)
        ->first();

        if($income) {
            return [
                'message' => 'income exists already for current period',
                'errorId' => 'PeriodIncomeExists'
            ];
        }

        $income = Income::create([
            'user_id' => $user->id,
            'period_id' => $period->id,
            'total' => $args['income'],
            'remainder' => $args['income']
        ]);

        $user->total_income += $args['income'];
        $user->save();

        return [
            'message' => 'income recorded successfully',
            'income' => $income
        ];
    }   
}
