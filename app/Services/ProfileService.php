<?php
namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileService {

    public function editInfo()
    {
        $data = Validator::make(request()->all(), [
            'name' => 'required',
            'avatar' => 'file|nullable',
            'city' => 'nullable|exists:city,name',
            'phone' => 'nullable'
        ])->validate();
        $userId = auth()->user()->id;
        $user = User::find($userId);
        if ($user) {
            if (isset($data['avatar'])) {
                $tmpAvatarPath =  Storage::disk('public')
                        ->getConfig()['root'] . '/tmp/avatar/' . $userId . '/';
                $avatarName = $userId . '.' . $data['avatar']->getClientOriginalExtension();
                if (file_exists($tmpAvatarPath)) {
                    $avatarPath = Storage::disk('public')
                            ->getConfig()['root'] . '/users/' . $userId .'/avatar/';
                    if(make_directory($avatarPath, 0777, true)) {
                        if (copy($tmpAvatarPath . $avatarName, $avatarPath . $avatarName)) {
                            $user->avatar =  '/users/' . $userId .'/avatar/' . $avatarName;
                        } else {
                            request()->session()->flash('error', 'Произошла ошибка при сохранении изображения');
                        }
                    }
                }
            }
            if ($data['phone']) {
                if (preg_match('#\+375\s\(\d{2}\)\s\d{3}-\d{2}-\d{2}#is', $data['phone'])) {
                    $user->phone = $data['phone'];
                } else {
                    request()->session()->flash('error', 'Номер телефона не соответстувет формату');
                }
            } else {
                $user->phone = "";
            }

            $user->name = $data['name'];
            $user->city = $data['city'];
            $user->save();
            auth()->setUser($user);
            request()->session()->now('success', 'Данные сохранены');
        }
    }

    public function editEmail()
    {
        $data = Validator::make(request()->all(), [
            'email' => 'required|email|unique:users'
        ])->validate();
        $userId = auth()->user()->id;
        $user = User::find($userId);
        if ($user) {
            $user->email = $data['email'];
            $user->save();
            auth()->setUser($user);
            request()->session()->now('success', 'Данные сохранены');
        }
    }

    public function editPassword()
    {
        $data = Validator::make(request()->all(), [
            'password'         => 'required|min:8',
            'password_confirm' => 'required|min:8|same:password'
        ])->validate();
        $userId = auth()->user()->id;
        $user = User::find($userId);
        if ($user) {
            $user->password = Hash::make($data['password']);
            $user->save();
            auth()->setUser($user);
            request()->session()->now('success', 'Данные сохранены');
        }
    }
}
?>
