<?php

declare(strict_types=1);

namespace TicTacToe\Core;

use TicTacToe\Core\Exceptions\GameException;

class Game
{
	private Board $board;
	private GameState $state;
	private PlayerManager $playerManager;

	public function __construct(
		Board $board,
		GameState $state,
		PlayerManager $playerManager
	) {
		$this->board = $board;
		$this->state = $state;
		$this->playerManager = $playerManager;
	}


	public function startNewGame(): void
	{
		$this->board->reset();
		$this->state->reset();
		$this->playerManager->reset();
	}

	public function surrenderGame(): void
	{
		$this->state->setWinner(
			$this->playerManager->getOpponentSymbol()
		);
	}

	public function isGameOver(): bool
	{
		return !$this->state->isPlaying();
	}

	public function getBoard(): Board
	{
		return $this->board;
	}

	public function getState(): GameState
	{
		return $this->state;
	}

	public function getPlayerManager(): PlayerManager
	{
		return $this->playerManager;
	}

	private function validateGameActive(): void
	{
		if (!$this->state->isPlaying()) {
			throw new GameException('Game is not active. Start a new game.');
		}
	}

	public function makeMove(string $playerSymbol, int $x, int $y): void
	{
		$this->validateGameActive();
		$this->playerManager->validatePlayerTurn($playerSymbol);
		$this->board->validateMove($x, $y);

		$this->board->setCell($x, $y, $playerSymbol);

		if ($this->board->hasWinningLine($playerSymbol)) {
			$this->state->setWinner($playerSymbol);
			return;
		}

		if ($this->board->isBoardFull()) {
			$this->state->setDraw();
			return;
		}

		$this->playerManager->switchActivePlayer();
	}
}
