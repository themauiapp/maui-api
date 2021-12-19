<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use App\Models\Period;
use App\Models\Income;
class UpdateIncome
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
                'errorId' => 'NoPeriodIncomeExists'
            ];
        }

        if($args['income'] <= $income->total) {
            return [
                'message' => 'period income cannot be reduced',
                'errorId' => 'InvalidIncomeValue'
            ];
        }

        $extraIncome = $args['income'] - $income->total;
        $income->total = $args['income'];
        $income->remainder += $extraIncome;
        $user->total_income += $extraIncome;
        $income->save();
        $user->save();

        return [
            'message' => 'income updated successfully',
            'income' => $income
        ];
    }
}
