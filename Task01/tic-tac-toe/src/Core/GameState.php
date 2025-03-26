<?php

declare(strict_types=1);

namespace TicTacToe\Core;

class GameState
{
	public const STATE_PLAYING = 'playing';
	public const STATE_WINNER = 'winner';
	public const STATE_DRAW = 'draw';

	private string $currentState = self::STATE_PLAYING;
	private ?string $winner = null;

	public function reset(): void
	{
		$this->currentState = self::STATE_PLAYING;
		$this->winner = null;
	}

	public function setWinner(string $playerSymbol): void
	{
		$this->currentState = self::STATE_WINNER;
		$this->winner = $playerSymbol;
	}

	public function setDraw(): void
	{
		$this->currentState = self::STATE_DRAW;
		$this->winner = null;
	}

	public function isPlaying(): bool
	{
		return $this->currentState === self::STATE_PLAYING;
	}

	public function isDraw(): bool
	{
		return $this->currentState === self::STATE_DRAW;
	}

	public function getWinner(): ?string
	{
		return $this->winner;
	}

	public function getCurrentState(): string
	{
		return $this->currentState;
	}
}
