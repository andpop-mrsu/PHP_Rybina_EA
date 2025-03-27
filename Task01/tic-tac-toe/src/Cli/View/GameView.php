<?php

declare(strict_types=1);

namespace TicTacToe\Cli\View;

use TicTacToe\Core\Board;
use TicTacToe\Core\GameState;
use TicTacToe\Core\PlayerManager;
use TicTacToe\Core\Exceptions\InvalidMoveException;

class GameView
{
	public function __construct(
		private Board $board,
		private GameState $state,
		private PlayerManager $playerManager
	) {}

	public function showWelcome(): void
	{
		echo "Welcome to Tic Tac Toe!\n";
		echo "Player X starts the game.\n\n";
		echo "Coordinates are entered as 'x y' (1-3), where:\n";
		echo "1 1 - top-left, 1 2 - top-center, 1 3 - top-right\n";
		echo "2 1 - middle-left, etc.\n\n";
	}

	public function showBoard(): void
	{
		echo $this->renderBoard();
	}

	public function showGameResult(): void
	{
		echo $this->getStatusMessage() . "\n";
	}

	public function showError(string $message): void
	{
		echo "Error: $message\n";
	}

	private function renderBoard(): string
	{
		$output = "+---+---+---+\n";

		for ($y = 0; $y < Board::SIZE; $y++) {
			$output .= "|";
			for ($x = 0; $x < Board::SIZE; $x++) {
				$cell = $this->board->getCell($x, $y);
				$output .= " " . ($cell === Board::EMPTY_CELL ? ' ' : $cell) . " |";
			}
			$output .= "\n+---+---+---+\n";
		}

		return $output;
	}

	public function askForMove(): array
	{
		while (true) {
			try {
				echo "Player {$this->playerManager->getCurrentPlayer()}, enter your move (x y, 1-3): ";
				$input = trim(fgets(STDIN));

				if (!preg_match('/^([1-3])\s+([1-3])$/', $input, $matches)) {
					throw new InvalidMoveException('Please enter two numbers from 1 to 3, separated by space');
				}

				return [(int)$matches[1] - 1, (int)$matches[2] - 1];
			} catch (InvalidMoveException $e) {
				$this->showError($e->getMessage());
			}
		}
	}

	private function getStatusMessage(): string
	{
		if ($this->state->isDraw()) {
			return "Game ended in a draw!";
		}

		if ($this->state->getWinner()) {
			return "Player '{$this->state->getWinner()}' won!";
		}

		return "Current player: '{$this->playerManager->getCurrentPlayer()}'";
	}
}
