<?php

namespace App\Http\Controllers;

use App\Models\DiscogsArtist;
use Illuminate\Http\Request;
use function Composer\Autoload\includeFile;

class ArtistController extends Controller
{

    public function index(Request $request, DiscogsArtist $artist)
    {
        $filePath = storage_path('app/public/discogs/releases/') . $artist->id. '/' . $artist->id . '.data';
        $releases = [];
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath));
            if (is_array($data)) {
                $releases = $data;
            }
        }
        return view('artists.index', compact('artist', 'releases'));
    }

}

