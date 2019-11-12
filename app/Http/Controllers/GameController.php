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
     * Return all songs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response_ok($this->gameService->getTracks());
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
