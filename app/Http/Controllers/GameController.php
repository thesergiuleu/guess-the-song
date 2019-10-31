<?php

namespace App\Http\Controllers;

use App\Services\GameService;
use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GameController extends Controller
{
    /**
     * @var mixed
     */
    private $spotifyChart;
    /**
     * @var GameService
     */
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService  = $gameService;
    }

    /**
     * Return the playing song and the three guess inputs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response_ok([
            'answers'       => $this->gameService->getTracks()['answers'],
            'preview_url'   => $this->gameService->getTracks()['preview_url'],
            'track_id'      => $this->gameService->getTracks()['track_id'],
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

}
