<?php

namespace App\GraphQL\Mutations;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Models\Avatar;

class UpdateAvatar
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
        $avatar = $args['avatar']->getRealPath();
        $cloudinary = new Cloudinary(config("app.cloudinary_url"));

        try {
            $response = $cloudinary->uploadApi()->upload($avatar, array('folder' => 'maui/avatars'));
        }
        catch(\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'errorId' => 'AvatarNotUpdated'
            ];
        }
        
        $avatarUrl = $response['secure_url'];
        $avatarPublicId = $response['public_id'];
        $userAvatar = Avatar::firstWhere('user_id', $user->id);
        if($userAvatar) {
            try {
                $cloudinary->uploadApi()->destroy($userAvatar->public_id);
            }
            catch(\Exception $e) {
                return [
                    'message' => $e->getMessage(),
                    'errorId' => 'AvatarNotUpdated'
                ];
            }
            $userAvatar->url = $avatarUrl;
            $userAvatar->public_id = $avatarPublicId;
            $userAvatar->save();
        }
        else {
            $user->avatar()->create([
                'url' => $avatarUrl,
                'public_id' => $avatarPublicId
            ]);
        }

        return [
            'message' => 'avatar updated successfully',
            'user' => $user->fresh()
        ];
    }
}
