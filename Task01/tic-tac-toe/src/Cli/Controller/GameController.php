<?php

declare(strict_types=1);

namespace TicTacToe\Cli\Controller;

use TicTacToe\Core\Game;
use TicTacToe\Cli\View\GameView;
use TicTacToe\Core\Exceptions\InvalidMoveException;

class GameController
{
	public function __construct(
		private Game $game,
		private GameView $view
	) {}

	public function start(): void
	{
		$this->view->showWelcome();

		while (!$this->game->isGameOver()) {
			try {
				$this->view->showBoard();
				[$x, $y] = $this->view->askForMove();

				$this->game->makeMove(
					$this->game->getPlayerManager()->getCurrentPlayer(),
					$x,
					$y
				);
			} catch (InvalidMoveException $e) {
				$this->view->showError($e->getMessage());
				continue;
			}
		}

		$this->view->showBoard();
		$this->view->showGameResult();
	}
}
