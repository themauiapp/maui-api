<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UpdateUser
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

        if(array_key_exists('timezone', $args)) {
            if(!$this->validateTimezone($args['timezone'])) {
                return [
                    'message' => 'timezone is not valid',
                    'errorId' => 'InvalidTimezone'
                ];
            }
        }

        if(array_key_exists('password', $args)) {
            $args['password'] = Hash::make($args['password']);
        }

        $user->fill($args);
        $user->save();

        return [
            'message' => 'user updated successfully',
            'user' => $user
        ];
    }

    public function validateTimezone($timezone) {
        $fileName = 'timezones.json';
        $fileHandler = fopen($fileName, 'r') or die('unable to open file');
        $data = fread($fileHandler, filesize($fileName));
        $timezones = json_decode($data, true);
        fclose($fileHandler);
        return in_array($timezone, $timezones);
    }
}
