<?php


namespace App\Services;


use App\Song;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

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

    public function handleDatabaseActions()
    {
        $lastScore      = ceil((30 - $this->data['time']) / 3); # Work out the number of points to add or subtract on this round
        $currentUser    = Auth::guard('api')->user();
        if ($currentUser) {
            if ((string)$this->data['answer'] === (string)session('answer')) {
                $song = Song::whereSpotifySongId($this->data['answer'])->first();
                if (!$song) {
                    $track = $this->spotifyService->getSpotifyApi()->getTrack($this->data['answer']);
                    $song = $this->prepareFieldsToSaveASong($track)->save();
                }
                $currentUser->songs->updateExistingPivot('song_id', ['user_id' => $currentUser->id]);
            }
            $this->updateUserData($currentUser, $lastScore);
        }
        $message = 'Right';
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
        $song->spotify_url  = $track->external_urls->spotify;
        $song->artist       = format_to_string($track->artists, 'name');

        return $song;
    }

    private function updateUserData(User $user, $score)
    {
        $user->seen_songs       = $user->seen_songs + 1;
        if ((string)$this->data['answer'] === (string)session('answer')) {
            $user->guessed_songs    = $user->guessed_songs + 1;
            $user->score            = $user->score + $score;
        } else {
            $user->score = $user->score - $score < 0 ? 0 : $user->score - $score;
        }

        $user->save();
    }

    public function getTracks()
    {
        if (!Cache::has('playlist')) {
            Cache::put(
                'playlist',
                $this->spotifyService->getSpotifyApi()->getPlaylistTracks('37i9dQZF1DX5Ejj0EkURtP'),
                2
            );
        }
        $spotifyChart = Cache::get('playlist');

        $recent = (Cache::has('recent_songs') ? Cache::get('recent_songs') : collect());
        $tracks = collect($spotifyChart->items)->reject(function($track) use ($recent) {
            return $this->_trackHasNoPreview($track->track) || $this->_trackIsRecent($track->track->id);
        })->shuffle()->take(3);

        $correctTrack   = $tracks->first();
        $correctAnswer  = $correctTrack->track->id;
        $recent         = $recent->push($correctAnswer);

        # Make sure we only store the last 20
//        if ($recent->count() >= 20) {
//            $recent = $recent->slice(1,20)->values();
//        }
        Cache::put(
            'recent_songs',
            $recent,
            5
        );
        $answers = $tracks->shuffle();

        return [
            'answers'        => $answers,
            'track_id'       => $correctAnswer,
            'preview_url'    => $correctTrack->track->preview_url
        ];
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
        $recent = (Cache::has('recent_songs') ? Cache::get('recent_songs') : collect());
        return $recent->contains($trackId);
    }
}
