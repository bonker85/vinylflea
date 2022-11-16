<?php
namespace App\Exports\Sheets;

use App\Models\DiscogsArtist;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserAdvertsStyle implements FromView, WithTitle, WithStyles
{
    private $list;

    public function __construct ($list, $adverts)
    {
        $this->list = $list;
        $this->adverts = $adverts;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension("A")->setWidth('70');
        $sheet->getColumnDimension('B')->setWidth('50');
        $sheet->getColumnDimension('C')->setWidth('20');
        $sheet->getColumnDimension('D')->setWidth('20');
        $sheet->getColumnDimension('E')->setWidth('10');
        $sheet->getColumnDimension('F')->setWidth('70');
        $sheet->getColumnDimension('G')->setWidth('10');
        $sheet->getColumnDimension('H')->setWidth('20');
        return [
            // Style the first row as bold text.
            1    => [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'FCF7B6'],]
               ],
        ];
    }

    /**
     * @return Builder
     */
    public function view() :View
    {
        $table = [];
        $row = 1;
        $title = [
            'exp-title' => 'Название',
            'exp-artist' => 'Исполнитель',
            'Стиль',
            'exp-price' => 'Цена (Руб.)',
            'exp-condition' => 'Оценка',
            'Детальная Информация',
            'Артикул',
            'Дата добавления'
        ];
     //   $i = 0;
        foreach ($this->adverts as $advert) {
          //  if ($i === 1) break;
           $table[$row][1] = $advert->name;
           if ($advert->discogs_author_ids) {
               $table[$row][2] = $this->getArtistsLink($advert->discogs_author_ids);
           } else if ($advert->author) {
               $table[$row][2] = $advert->author;
           } else {
               $table[$row][2] = 'unknown';
           }
           $table[$row][3] = $advert->sname;
           $table[$row][4] = $advert->price;
           $table[$row][5] = $advert->condition;
           $table[$row][6] = [[
               'link' => route('vinyls.details', $advert->url),
               'name' => $advert->name
           ]];
           $table[$row][7] = $advert->sku;
           $table[$row][8] = date('dmY',strtotime($advert->created_at));
          // $i++;
           $row++;
        }
        return view('excel.adverts', compact('table', 'title'));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->list;
    }
    //@todo Дублирующийся метод из DiscogsService почему-то на прямую если использовать баг открытия
    private function getArtistsLink($discogsArtistIds)
    {
        if ($discogsArtistIds) {
            $systemId = 194;
            if ($discogsArtistIds == $systemId) return "Various (Сборник)";
            if ($discogsArtistIds != $systemId) {
                $authors_ids = str_replace([',' . $systemId, $systemId . ','], '', $discogsArtistIds);
                $authorLists = explode(',', $authors_ids);
                $links = [];
                foreach ($authorLists as $artistId) {
                    $discogsAuthor = DiscogsArtist::select('name')->where('discogs_artist_id', $artistId)->first();
                    if ($discogsAuthor) {
                        $links[] =  ['link' => route('artist', $artistId), 'name' =>  $discogsAuthor->name];
                    }
                }
                return $links;
            }
        }
        return false;
    }
}
?>
