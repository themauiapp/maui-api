<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\Request;
use App\Traits\UpdateUserAvatar;

class UpdateAvatar
{
    use UpdateUserAvatar;

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
        $avatar = $args['avatar']->getRealPath();
        [$updated, $message] = $this->updateUserAvatar($user, $avatar);

        if(!$updated) {
            return [
                'message' => $message,
                'errorId' => 'AvatarNotUpdated',
            ];
        }

        return [
            'message' => 'Avatar updated successfully',
            'user' => $user->fresh()
        ];
    }
}
