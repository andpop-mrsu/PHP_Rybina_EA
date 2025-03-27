<?php

declare(strict_types=1);

namespace TicTacToe\Core;

use TicTacToe\Core\Exceptions\InvalidMoveException;

class Board
{
	public const SIZE = 3;
	public const EMPTY_CELL = '-';

	private array $cells;

	public function __construct()
	{
		$this->reset();
	}

	public function reset(): void
	{
		$this->cells = array_fill(
			0, // Начальный индекс (0 вместо 1)
			self::SIZE, // Количество строк (3)
			array_fill(
				0, // Начальный индекс (0 вместо 1)
				self::SIZE, // Количество колонок (3)
				self::EMPTY_CELL // Значение по умолчанию ('-')
			)
		);
	}

	public function setCell(int $x, int $y, string $symbol): void
	{
		$this->validateCoordinates($x, $y);
		$this->cells[$y][$x] = $symbol;
	}

	public function getCell(int $x, int $y): string
	{
		$this->validateCoordinates($x, $y);
		return $this->cells[$y][$x];
	}

	public function validateMove(int $x, int $y): void
	{
		$this->validateCoordinates($x, $y);
		$this->validateCellEmpty($x, $y);
	}

	private function validateCoordinates(int $x, int $y): void
	{
		if ($x < 0 || $x >= self::SIZE || $y < 0 || $y >= self::SIZE) {
			throw new InvalidMoveException('Invalid board position');
		}
	}

	private function getAllLines(): array
	{
		$lines = [];

		foreach ($this->cells as $row) {
			$lines[] = $row;
		}

		for ($x = 0; $x < self::SIZE; $x++) {
			$column = [];
			for ($y = 0; $y < self::SIZE; $y++) {
				$column[] = $this->cells[$y][$x];
			}
			$lines[] = $column;
		}

		$diagonal1 = [];
		$diagonal2 = [];
		for ($i = 0; $i < self::SIZE; $i++) {
			$diagonal1[] = $this->cells[$i][$i];
			$diagonal2[] = $this->cells[$i][self::SIZE - 1 - $i];
		}
		$lines[] = $diagonal1;
		$lines[] = $diagonal2;

		return $lines;
	}

	private function validateCellEmpty(int $x, int $y): void
	{
		if (!$this->isCellEmpty($x, $y)) {
			throw new InvalidMoveException('Cell is already occupied');
		}
	}

	public function isCellEmpty(int $x, int $y): bool
	{
		return $this->getCell($x, $y) === self::EMPTY_CELL;
	}

	public function hasWinningLine(string $symbol): bool
	{
		$winningLine = str_repeat($symbol, self::SIZE);

		foreach ($this->getAllLines() as $line) {
			if (implode('', $line) === $winningLine) {
				return true;
			}
		}

		return false;
	}

	public function isBoardFull(): bool
	{
		foreach ($this->cells as $row) {
			if (in_array(self::EMPTY_CELL, $row)) {
				return false;
			}
		}
		return true;
	}
}
