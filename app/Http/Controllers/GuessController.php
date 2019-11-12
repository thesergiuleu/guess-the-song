<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuessRequest;
use App\Services\{ GameService, SpotifyService };
use Illuminate\Support\Facades\Auth;

class GuessController extends Controller
{
    /**
     * @var SpotifyService
     */
    private $spotifyService;
    /**
     * @var GameService
     */
    private $gameService;
    /**
     * @var GuessRequest
     */
    private $guessRequest;

    public function __construct(GuessRequest $guessRequest, SpotifyService $spotifyService, GameService $gameService)
    {
        $this->guessRequest     = $guessRequest;
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
        $this->gameService->setData(
            $this->guessRequest->only(['answer', 'time', 'is_correct', 'last_score'])
        );

        return response_ok(["score" => $this->gameService->handleDatabaseActions()]);
    }


}
