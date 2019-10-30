<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GameController extends Controller
{
    /**
     * @var mixed
     */
    private $spotifyChart;

    public function __construct(SpotifyService $spotifyService)
    {
        if (!Cache::has('playlist')) {
            Cache::put(
                'playlist',
                $spotifyService->getSpotifyApi()->getPlaylistTracks('37i9dQZF1DX5Ejj0EkURtP'),
                2
            );
        }

        $this->spotifyChart = Cache::get('playlist');
    }

    /**
     * Return the playing song and the three guess inputs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        # Get our session of recently used tracks
        $recent = (session('recent_songs') ?: collect());
        $tracks = collect($this->spotifyChart->items)->reject(function($track) use ($recent) {
            return $this->_trackHasNoPreview($track->track) || $this->_trackIsRecent($track->track->id);
        })->shuffle()->take(3);

        $correctTrack   = $tracks->first();
        $correctAnswer  = $correctTrack->track->id;
        $recent         = $recent->push($correctAnswer);

        # Make sure we only store the last 20
        if ($recent->count() >= 20) {
            $recent = $recent->slice(1,20)->values();
        }
        session([
            'recent_songs' => $recent
        ]);

        $answers = $tracks->shuffle();
        session(['answer' => $correctAnswer]);

        # Auth::user()->song()->increment('songs_seen');

        return response_ok([
            'answers'       => $answers,
            'last_score'    => session('last_score') ?: '',
            'track'         => $correctTrack->track,
            'message'       => session('message') ?: '',
        ]);
    }

    /**
     * Handle a timeout
     */
    public function timeout()
    {
        return response_ok([
            'message' => 'Timeout'
        ]);
    }
    /**
     * Test to see if a track has a valid preview URL
     *
     * @param $track
     * @return bool
     */
    private function _trackHasNoPreview($track)
    {
        return is_null($track->preview_url);
    }

    /**
     * Test to see if a track has been recently used as a correct answer
     *
     * @param $trackId
     * @return bool
     */
    private function _trackIsRecent($trackId)
    {
        $recent = (session('recent_songs') ?: collect());
        return $recent->contains($trackId);
    }
}
