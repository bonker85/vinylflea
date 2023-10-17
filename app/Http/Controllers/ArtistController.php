<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\DiscogsArtist;
use App\Services\Utility\GoogleTranslateService;
use Illuminate\Http\Request;
use function Composer\Autoload\includeFile;

class ArtistController extends Controller
{

    public function index(Request $request, DiscogsArtist $artist)
    {
        if ($artist->discogs_artist_id == 194) {
            abort('404');
        }
        $filePath = storage_path('app/public/discogs/releases/') . $artist->id. '/' . $artist->id . '.data';
        $releases = [];
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath));
            if (is_array($data)) {
                $releases = $data;
            }
        }
        $adverts = Advert::where('discogs_author_ids', $artist->discogs_artist_id)->get();
        return view('artists.index', compact('artist', 'releases', 'adverts'));
    }

    public function edit(Request $request, DiscogsArtist $artist)
    {
        if ($request->profile) {
            $trans = new GoogleTranslateService();
            $source = 'en';
            $target = 'ru';
            $artist->profile = $request->profile;
            $artist->profile_translate = $trans->translate($source, $target, $request->profile);
            $artist->save();
        }
        return redirect()->route('artist', $artist->discogs_artist_id);
    }

    public function list()
    {
        $artists = DiscogsArtist::select(['discogs_artist_id', 'name'])->where('id', '!=', 1)->orderBy('name')->get();
        return view('artists.list', compact('artists'));
    }
}

