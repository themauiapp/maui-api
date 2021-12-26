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
        $date = isset($args['date']) ? strtotime($args['date']) : strtotime(date('Y-m-d'));
        $month = date('n', $date);
        $year = date('Y', $date);

        $period = Period::firstOrCreate([
            'month' => $month,
            'year' => $year
        ], []);

        $income = Income::where('user_id', $user->id)
        ->where('period_id', $period->id)
        ->first();

        return $income;
    }
}
