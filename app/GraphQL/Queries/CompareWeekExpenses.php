<?php

namespace App\GraphQL\Queries;

use Illuminate\Http\Request;
use App\Models\Expense;

class CompareWeekExpenses
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    protected $user; 

    public function __construct(Request $request)
    {
        $this->user = $request->user();
    }

    public function __invoke($_, array $args)
    {
        date_default_timezone_set($this->user->timezone);
        
        $weekOne = $args['weekOne'];
        $weekTwo = $args['weekTwo'];
        $weekOneExpenses = [];
        $weekTwoExpenses = [];
        $weekOneSum = 0;
        $weekTwoSum = 0;

        for($i = 0; $i <= 7; $i++) {
            $date = strtotime($weekOne) + ($i * 86400);
            $date = date('Y-m-d', $date);
            $sum = $this->getDateSum($date);
            $weekOneSum += $sum;
            array_push($weekOneExpenses, $sum);

            if($i === 7) {
                $weekOneEnd = $date;
            }
        }
        

        for($i = 0; $i <= 7; $i++) {
            $date = strtotime($weekTwo) + ($i * 86400);
            $date = date('Y-m-d', $date);
            $sum = $this->getDateSum($date);
            $weekTwoSum += $sum;
            array_push($weekTwoExpenses, $sum);

            if($i === 7) {
                $weekTwoEnd = $date;
            }
        }

        $weeks = [$weekOneExpenses, $weekTwoExpenses];
        $sums = [$weekOneSum, $weekTwoSum];
        $startDates = [$weekOne, $weekTwo];
        $endDates = [$weekOneEnd, $weekTwoEnd];

        return [
            'weeks' => $weeks,
            'sums' => $sums,
            'startDates' => $startDates,
            'endDates' => $endDates
        ];
    }

    public function getDateSum($date)
    {   
        $sum = Expense::where('user_id', $this->user->id)
        ->where('created_at', '>=', $date.' '.'00:00:00')
        ->where('created_at', '<=', $date.' '.'23:59:59')
        ->sum('amount');
        
        return $sum;
    }
}
