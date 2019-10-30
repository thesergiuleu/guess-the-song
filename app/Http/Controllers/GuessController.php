<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use App\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuessController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var SpotifyService
     */
    private $spotifyService;

    public function __construct(Request $request, SpotifyService $spotifyService)
    {

        $this->request = $request;
        $this->spotifyService = $spotifyService;
    }

    /**
     * Handle the user's guess
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $data = $this->request->only(['answer', 'time']);
        # Work out the number of points to add or subtract on this round
        $lastScore = ceil((30 - $data['time']) / 3);
        $song = Song::whereSpotifySongId($data['answer'])->first();
        if (!$song) {
            $track = $this->spotifyService->getSpotifyApi()->getTrack($data['answer']);
            $song  = $this->prepareFieldsToSaveASong($track);
            $song->save();
        }

        dd($song);
//        Auth::user();
//
//        return response()->json($track);

        // Check to see if they got this one right
        if ($this->request->get('answer') == session('answer')) {
            // Update the database
            dd(Auth::user()->songs);
            if (Auth::check()) {
                dd(Auth::user()->songs);
//                if (is_null(Song::where('user_id', Auth::id())->first())) {
//                    Song::create([
//                        'user_id' => Auth::id(),
//                        'songs_correct' => DB::raw('songs_correct + 1'),
//                        'score' => DB::raw('score + '.$lastScore),
//                    ]);
//                } else {
//                    Song::where('user_id', Auth::id())->update([
//                        'songs_correct' => DB::raw('songs_correct + 1'),
//                        'score' => DB::raw('score + '.$lastScore),
//                    ]);
//                }
            }
            $message = 'Right';
        } else {
//            if (Auth::check()) {
//                $userScore = Auth::user()->song()->first();
//                if ($userScore) {
//                    if ($userScore->score - $lastScore > 0) {
//                        Auth::user()->song()->decrement('score', $lastScore);
//                    } else {
//                        Auth::user()->song()->decrement('score', $userScore->score);
//                    }
//                }
//            }
            $message = 'Wrong';
        }
        return response_ok([
            'last_score'    => $lastScore,
            'message'       => $message,
        ]);
    }

    private function prepareFieldsToSaveASong($track)
    {
        $song               = new Song();
        $song->name         = $track->name;
        $song->album        = $track->album->name;
        $song->preview_url  = $track->preview_url;
        $song->spotify_url  = $track->external_urls->spotify;
        $song->artist       = rtrim($this->prepareArtistField($track->artists), ', ');

        return $song;
    }

    private function prepareArtistField($artists)
    {
        $return = '';
        foreach ($artists as $artist) {
            $return .= $artist->name . ', ';
        }
        return $return;
    }
}
