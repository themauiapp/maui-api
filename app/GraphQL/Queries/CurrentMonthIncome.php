<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Period;
use App\Models\Income;

class CurrentMonthIncome
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
        date_default_timezone_set($user->timezone);
        $month = date('n');
        $year = date('y');

        $period = Period::firstOrCreate([
            'month' => $month,
            'year' => $year
        ], []);

        $income = Income::where('user_id', $user->id)
        ->where('period_id', $period->id)
        ->first();

        if(!$income) {
            return [
                'message' => 'no income exists for current period',
                'errorId' => 'PeriodIncomeDoesNotExist'
            ];
        }

        $income->total = number_format($income->total);
        $income->remainder = number_format($income->remainder);
        return $income;
    }
}
