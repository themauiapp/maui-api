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
        
        $dateOne = $args['dateOne'];
        $dateTwo = $args['dateTwo'];
        $weekOne = [];
        $weekTwo = [];
        $weekOneSum = 0;
        $weekTwoSum = 0;

        for($i = 0; $i <= 7; $i++) {
            $date = strtotime($dateOne) + ($i * 86400);
            $date = date('Y-m-d', $date);
            $sum = $this->getDateSum($date);
            $weekOne[$date] = $sum;
            $weekOneSum += $sum;
        }
        

        for($i = 0; $i <= 7; $i++) {
            $date = strtotime($dateTwo) + ($i * 86400);
            $date = date('Y-m-d', $date);
            $sum = $this->getDateSum($date);
            $weekTwo[$date] = $sum;
            $weekTwoSum += $sum;
        }

        $sums = [$weekOneSum, $weekTwoSum];

        return [
            'weeks' => array($weekOne, $weekTwo),
            'sums' => $sums,
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
