<?php

namespace App\Services;

use App\{ Song, User };
use Illuminate\Support\Facades\{ Auth, Request };

class GameService
{
    /**
     * @var SpotifyService
     */
    private $spotifyService;
    /**
     * @var Request
     */
    private $request;

    private $data = [];

    public function __construct(SpotifyService $spotifyService, Request $request)
    {
        $this->spotifyService = $spotifyService;
        $this->request        = $request;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Return current score of player, and saves user/song data if user is logged in
     *
     * @return float|mixed
     */
    public function handleDatabaseActions()
    {
        $score      = ceil((30 - $this->data['time']) / 3); # Work out the number of points to add or subtract on this round
        $currentUser    = Auth::guard('api')->user();
        if ($currentUser) {
            if ($this->data['is_correct']) {
                $song = Song::whereSpotifySongId($this->data['answer'])->first();
                if (!$song) {
                    $track  = $this->spotifyService->getSpotifyApi()->getTrack($this->data['answer']);
                    $song   = $this->prepareFieldsToSaveASong($track);
                    $song->save();
                }
                $currentUser->songs()->syncWithoutDetaching($song->id);
            }

            $this->updateUserData($currentUser, $score);

            return $currentUser->score;
        }

        return $this->data['last_score'] + $score;
    }

    /**
     * Get 100 songs from spotify API
     *
     * @return array
     */
    public function getTracks()
    {
        $spotifyChart = $this->spotifyService->getSpotifyApi()->getPlaylistTracks('37i9dQZF1DX5Ejj0EkURtP');

        $tracks = collect($spotifyChart->items)->reject(function($track) {
            return $this->_trackHasNoPreview($track->track) || $this->_trackHaveBeenGuessed($track->track);
        })->shuffle();

        return ['tracks' => $tracks];
    }
    /**
     * Get Song fields ready to be saved
     *
     * @param $track
     * @return Song
     */
    private function prepareFieldsToSaveASong($track)
    {
        $song               = new Song();
        $song->name         = $track->name;
        $song->album        = $track->album->name;
        $song->preview_url  = $track->preview_url;
        $song->preview_url  = $track->preview_url;
        $song->spotify_url  = $track->external_urls->spotify;
        $song->artist       = format_to_string($track->artists, 'name');
        $song->spotify_song_id       = $track->id;

        return $song;
    }

    private function updateUserData(User $user, $score)
    {
        $user->seen_songs       = $user->seen_songs + 1;
        if ($this->data['is_correct']) {
            $user->guessed_songs    = $user->guessed_songs + 1;
            $user->score            = $user->score + $score;
        } else {
            $user->score = $user->score - $score < 0 ? 0 : $user->score - $score;
        }

        $user->save();
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
     * Test to see if logged user already guessed this song
     *
     * @param $track
     * @return bool
     */
    private function _trackHaveBeenGuessed($track)
    {
        if ($user = Auth::guard('api')->user()) {
            return $user->songs()->where('spotify_song_id', $track->id)->exists();
        }
        return false;
    }
}
