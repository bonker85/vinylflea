<?php

namespace App\Exports;

use App\Exports\Sheets\UserAdvertsStyle;
use App\Models\Advert;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserAdvertsExport implements WithMultipleSheets
{
    use Exportable;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $styles = [];
        $userAdverts = Advert::select('adverts.*', 's.name AS sname')
            ->where('user_id', $this->userId)
            ->where('status', 1)
            ->join('styles AS s', 's.id', '=', 'adverts.style_id')
            ->orderBy('sname')
            ->orderBy('name')
            ->get();
        foreach ($userAdverts as $advert) {
            $styles[$advert->sname][] = $advert;
        }
        foreach ($styles as $style => $adverts) {
            $sheets[] = new UserAdvertsStyle($style, $adverts);
        }

        return $sheets;
    }
}
