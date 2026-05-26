<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;

class PlayerDashboardController extends Controller
{
    public function index()
    {
        return view('player.dashboard', [
            'player' => auth()->user(),
        ]);
    }
}