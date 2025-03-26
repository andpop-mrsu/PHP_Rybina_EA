<?php

declare(strict_types=1);

namespace TicTacToe\Core;

use TicTacToe\Core\Exceptions\InvalidPlayerException;

class PlayerManager
{
	public const PLAYER_X = 'X';
	public const PLAYER_O = 'O';

	private string $currentPlayer = self::PLAYER_X;

	public function reset(): void
	{
		$this->currentPlayer = self::PLAYER_X;
	}

	public function validatePlayerTurn(string $playerSymbol): void
	{
		$normalizedSymbol = strtoupper($playerSymbol);

		if (!in_array($normalizedSymbol, [self::PLAYER_X, self::PLAYER_O])) {
			throw new InvalidPlayerException('Invalid player symbol');
		}

		if ($normalizedSymbol !== $this->currentPlayer) {
			throw new InvalidPlayerException("It's not your turn");
		}
	}

	public function switchActivePlayer(): void
	{
		$this->currentPlayer = $this->getOpponentSymbol();
	}

	public function getCurrentPlayer(): string
	{
		return $this->currentPlayer;
	}

	public function getOpponentSymbol(): string
	{
		return $this->currentPlayer === self::PLAYER_X
			? self::PLAYER_O
			: self::PLAYER_X;
	}
}
