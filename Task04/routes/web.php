<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'play'])->name('game.play');
Route::post('/game', [GameController::class, 'store'])->name('game.store');
Route::get('/game/result/{game}', [GameController::class, 'result'])->name('game.result');

Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show');
