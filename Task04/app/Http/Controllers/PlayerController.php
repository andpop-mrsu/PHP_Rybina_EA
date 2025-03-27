<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
	public function index()
	{
		$players = Player::withCount('games')->latest()->get();
		return view('players.index', compact('players'));
	}

	public function show(Player $player)
	{
		$games = $player->games()->latest()->get();
		return view('players.show', compact('player', 'games'));
	}
}
