<?php
namespace App\Services;


use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileService {

    public function editInfo()
    {
        $data = Validator::make(request()->all(), [
            'name' => 'required|max:20',
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
                            $user->cdn_status = 0;
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

    public  function getAdvertDialogMessages($advertDialogId)
    {
        $userId = auth()->user()->id;
        if ($userId) {
            return Message::select()->where('advert_dialog_id', $advertDialogId)
                ->where(function($q) use ($userId) {
                    $q->where('from_id', $userId)->orWhere('to_id', $userId);
                })
                ->orderBy('created_at')->get();
        }
        return [];
    }

    public function generateChatBlockForAjaxQuery($advertDialog)
    {
        $html = '';
        $advertDialogId = $advertDialog->id;
        $messages = Message::select()->where('advert_dialog_id', $advertDialogId)->orderBy('created_at')->get();
        $avatar = '';
        if (auth()->user()->id == $advertDialog->from_user_id) {
            $avatar = $advertDialog->toUser->avatar;
            $user = $advertDialog->toUser;
        } else {
            $avatar = $advertDialog->fromUser->avatar;
            $user = $advertDialog->fromUser;
        }
        foreach ($messages as $message) {
            $html .=
                '<li class="chat-tip ' .
                    (($message->from_id == auth()->user()->id) ? 'right-user' : 'left-user')
                .'">';
            $html .= '<div class="mess-dt">' . $message->getFormatDate() . '</div>';
            if (($message->to_id == auth()->user()->id)) {
                if ($avatar) {
                    $html .= '<div>
                                <img class="avatar-mess" src="' . cdn_url(asset( 'storage' . $avatar), $user) . '"/>
                            </div>';
                } else {
                    $html .= '<div>
                                <img class="avatar-mess" src="' . asset('/assets/images/avatars/no-avatar.png') . '"/>
                            </div>';
                }

            }
            $html .= $message->message . '</li>';
        }
        return $html;
    }

    public function clearAdvertDialogDontViewMessages($advertDialog)
    {
        if (auth()->check()) {
            $userId = auth()->user()->id;
            if ($advertDialog->from_user_id == $userId) {
                $advertDialog->count_not_view_user_from = 0;
            } else if ($advertDialog->to_user_id == $userId) {
                $advertDialog->count_not_view_user_to = 0;
            }
            $advertDialog->save();
        }
    }
}
?>
