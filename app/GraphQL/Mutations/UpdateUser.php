<?php

namespace App\GraphQL\Mutations;

use App\Traits\UpdateUserAvatar;
use App\Traits\ValidateTimezone;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class UpdateUser
{
    use UpdateUserAvatar, ValidateTimezone;
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

        if(isset($args['avatar'])) {
            $this->updateUserAvatar($user, $args['avatar']->getRealPath());            
        }

        $user->fill($args);
        $user->save();

        return [
            'message' => 'user updated successfully',
            'user' => $user
        ];
    }
}
