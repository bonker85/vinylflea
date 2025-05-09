<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;
    const MY_USERS_IDS = [1, 4, 6, 11];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'city',
        'avatar',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public static function getRoles() :array {
        return [
            self::ROLE_ADMIN => 'Администратор',
            self::ROLE_USER => 'Пользователь'
        ];
    }

    public static function isAdmin()
    {
        return (auth()->user() && (int)auth()->user()->role_id === self::ROLE_ADMIN);
    }

    public static function isMyUsers()
    {
        return (auth()->check() && in_array(auth()->user()->id, self::MY_USERS_IDS));
    }
    public static function isMyUserId($userId)
    {
        return in_array($userId, self::MY_USERS_IDS);
    }

   public function isBan()
   {
       return BanUserList::where('user_id', $this->id)->first();
   }

   public function isDialog($toUserId)
   {
       return AdvertDialog::select()
           ->where('from_user_id', 1)
           ->where('to_user_id', $toUserId)
           ->first();
   }
}
