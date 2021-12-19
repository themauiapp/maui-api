<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Period;
use App\Models\Expense;

class CompareMonthExpenses
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    protected $user;

    public function __construct(Request $request) {
        $this->user = $request->user();
    } 

    public function __invoke($_, array $args)
    {
        $dateOne = $args['dateOne'];
        $dateTwo = $args['dateTwo'];
        $monthOne = $this->parseMonthExpenses($dateOne);
        $monthTwo = $this->parseMonthExpenses($dateTwo);

        return [
            'months' => array($monthOne, $monthTwo)
        ];
    }

    public function parseMonthExpenses($date) {
        $date = strtotime($date);
        $month = date('n', $date);
        $year = date('Y', $date);

        $period = Period::where('month', $month)
        ->where('year', $year)
        ->first();

        $month = date('m', $date);
        $numDays = date('t', $date);
        $firstDay = "$year-$month-01";
        $lastDay = "$year-$month-$numDays";
        $interval = round($numDays / 5);

        if(!$period) {
            return $this->getMonthExpenses($firstDay, $lastDay, $interval, NULL);
        }

        $income = Income::where('user_id', $this->user->id)
        ->where('period_id', $period->id)
        ->get();
        
        return $this->getMonthExpenses($firstDay, $lastDay, $interval, $income);
    }

    public function getMonthExpenses($firstDay, $lastDay, $interval, $income) {
        $data = [];

        $data[$firstDay] = $income ? $this->getAmountSpent($firstDay) : 0;
        for($i = 1; $i <= 4; $i++) {
            $date = strtotime($firstDay) + ($i * $interval * 86400);
            $date = date('Y-m-d', $date);
            $data[$date] = $income ? $this->getAmountSpent($date) : 0;
        }
        $data[$lastDay] = $income ? $this->getAmountSpent($lastDay) : 0;
        return $data;
    }

    public function getAmountSpent($date) {
        return Expense::where('user_id', $this->user->id)
        ->where('created_at', '>', "$date 00:00:00")
        ->where('created_at', '<=', "$date 23:59:59")
        ->sum('amount');
    }
}
