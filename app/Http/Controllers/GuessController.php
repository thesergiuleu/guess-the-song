<?php

namespace App\Http\Controllers;

use App\Services\GameService;
use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    private $currentUser;
    /**
     * @var GameService
     */
    private $gameService;

    public function __construct(Request $request, SpotifyService $spotifyService, GameService $gameService)
    {

        $this->request          = $request;
        $this->spotifyService   = $spotifyService;
        $this->gameService      = $gameService;
    }

    /**
     * Handle the user's guess
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        dd(Cache::getStore());

        $data = $this->request->only(['answer', 'time']);
        $this->gameService->setData($data);
        $this->gameService->handleDatabaseActions();

//        return response_ok([
//            'last_score'    => $lastScore,
//            'message'       => $message,
//        ]);
    }


}
