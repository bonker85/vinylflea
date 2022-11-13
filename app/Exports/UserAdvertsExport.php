<?php

namespace App\Exports;

use App\Exports\Sheets\UserAdvertsStyle;
use App\Models\Advert;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserAdvertsExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets():array
    {
        $sheets = [];
        $lists = [];
        $selectAdverts = Advert::select('adverts.*', 's.name AS sname')
            ->where('status', 1)
            ->join('styles AS s', 's.id', '=', 'adverts.style_id');
        if (isset($this->data['users_ids']) && !empty($this->data['users_ids'])) {
            $selectAdverts->whereRaw('user_id IN (' . implode(',', $this->data['users_ids']) . ')');
        } else {
            //всех моих пользователей кроме админа
            $myUsersIds = User::MY_USERS_IDS;
            unset($myUsersIds[0]);
            $selectAdverts->whereRaw('user_id IN (' . implode(',', $myUsersIds) . ')');
        }
        if (isset($this->data['styles_ids']) && !empty($this->data['styles_ids'])) {
            $selectAdverts->where('style_id', implode(',', $this->data['styles_ids']));
        }
        if ($this->data['sep'] == 'none') {
            $needStylesSeparation = false;
        } else {
            $needStylesSeparation = true;
        }

        if ($this->data['sep'] == 'styles') {
            $selectAdverts->orderBy('sname');
        }
        $adverts = $selectAdverts->orderBy('name')->get();
        if ($needStylesSeparation) {
            if ($this->data['sep'] == 'styles') {
                foreach ($adverts as $advert) {
                    $lists[$advert->sname][] = $advert;
                }
            } else {
                foreach ($adverts as $advert) {
                    $lists[$advert->user->email][] = $advert;
                }
            }
        } else {
            $lists['Все стили'] = $adverts;
        }
        foreach ($lists as $list => $adverts) {
            if ($list == 'Trash Metal') {
            }
            $sheets[] = new UserAdvertsStyle($list, $adverts);
        }
        return $sheets;
    }
}
