<?php 

namespace App\Traits;

use App\Models\Avatar;
use App\Models\User;
use Cloudinary\Cloudinary;

trait UpdateUserAvatar {
    public function updateUserAvatar($user, $avatar)
    {
        $cloudinary = new Cloudinary(config("app.cloudinary_url"));

        try {
            $response = $cloudinary->uploadApi()->upload($avatar, array('folder' => 'maui/avatars'));
        }
        catch(\Exception $e) {
            return [false, $e->getMessage()];
        }
        
        $avatarUrl = $response['secure_url'];
        $avatarPublicId = $response['public_id'];
        $userAvatar = Avatar::firstWhere('user_id', $user->id);
        if($userAvatar) {
            try {
                if($userAvatar->public_id) {
                    $cloudinary->uploadApi()->destroy($userAvatar->public_id);
                }
            }
            catch(\Exception $e) {
                return [false, $e->getMessage()];
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

        return [true, 'successful'];
    }
}