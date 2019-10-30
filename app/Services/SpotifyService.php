<?php


namespace App\Services;


use Illuminate\Support\Facades\Cache;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyService
{
    protected $spotifyClient;
    protected $spotifyApi;

    public function __construct()
    {
        // Attempt to get access token
        if (!Cache::has('accessToken')) {
            // Create the Spotify Client
            $this->spotifyClient = new Session(
                env('SPOTIFY_CLIENT_ID'),
                env('SPOTIFY_CLIENT_SECRET')
            );
            // Attempt to get client_credentials token
            if ($this->spotifyClient->requestCredentialsToken()) {
                $tokenExpiryMinutes = floor(($this->spotifyClient->getTokenExpiration() - time()) / 60);
                Cache::put(
                    'accessToken',
                    $this->spotifyClient->getAccessToken(),
                    $tokenExpiryMinutes
                );
            }
        }
        // Use access token to connect to API
        $this->spotifyApi = new SpotifyWebAPI();
        $this->spotifyApi->setAccessToken(Cache::get('accessToken'));
    }

    public function getSpotifyApi()
    {
        return $this->spotifyApi;
    }
    public function getSpotifyClient()
    {
        return $this->spotifyClient;
    }
}
