<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;

class GameController extends Controller
{
	public function index()
	{
		$games = Game::with('player')->latest()->get();
		return view('games.index', compact('games'));
	}

	public function play()
	{
		$expression = $this->generateExpression();
		$correctAnswer = eval('return ' . $expression . ';');

		return view('game', [
			'expression' => $expression,
			'correctAnswer' => $correctAnswer
		]);
	}

	public function store(Request $request)
	{
		$request->validate([
			'player_name' => 'required|string|max:255',
			'user_answer' => 'required|numeric',
			'expression' => 'required|string',
			'correct_answer' => 'required|numeric',
		]);

		$player = Player::firstOrCreate(['name' => $request->player_name]);

		$game = new Game([
			'expression' => $request->expression,
			'correct_answer' => $request->correct_answer,
			'user_answer' => $request->user_answer,
			'is_correct' => abs($request->user_answer - $request->correct_answer) < 0.0001,
		]);

		$player->games()->save($game);

		return redirect()->route('game.result', $game);
	}

	public function result(Game $game)
	{
		return view('result', compact('game'));
	}

	private function generateExpression(): string
	{
		$operators = ['+', '-', '*'];
		$expression = '';

		for ($i = 0; $i < 4; $i++) {
			$expression .= rand(1, 20);
			if ($i < 3) {
				$expression .= $operators[array_rand($operators)];
			}
		}

		return $expression;
	}
}
