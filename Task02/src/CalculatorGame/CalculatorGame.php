<?php

namespace CalculatorGame;

class CalculatorGame
{
	private const MIN_OPERAND = 1;
	private const MAX_OPERAND = 20;
	private const OPERAND_COUNT = 4;
	private const OPERATORS = ['+', '-', '*'];

	public function generateExpression(): array
	{
		do {
			$numbers = $this->generateRandomNumbers();
			$operators = $this->generateRandomOperators();
			$expression = $this->buildExpression($numbers, $operators);
			$correctAnswer = $this->evaluateExpression($expression);

			// Повторяем генерацию если получилось отрицательное число
		} while ($correctAnswer < 0 || $correctAnswer > 100); // Добавляем разумные пределы

		return [
			'expression' => $expression,
			'correct_answer' => $correctAnswer
		];
	}

	private function generateRandomNumbers(): array
	{
		return array_map(
			fn() => rand(self::MIN_OPERAND, self::MAX_OPERAND),
			range(1, self::OPERAND_COUNT)
		);
	}

	private function generateRandomOperators(): array
	{
		return array_map(
			fn() => self::OPERATORS[array_rand(self::OPERATORS)],
			range(1, self::OPERAND_COUNT - 1)
		);
	}

	private function buildExpression(array $numbers, array $operators): string
	{
		$expression = (string)$numbers[0];
		for ($i = 0; $i < count($operators); $i++) {
			$expression .= ' ' . $operators[$i] . ' ' . $numbers[$i + 1];
		}
		return $expression;
	}

	private function evaluateExpression(string $expression): int
	{
		// Упрощенный и более надежный способ вычисления
		$tokens = explode(' ', $expression);
		$tokens = array_values(array_filter($tokens));

		// Обрабатываем умножение сначала
		for ($i = 1; $i < count($tokens); $i += 2) {
			if ($tokens[$i] === '*') {
				$result = $tokens[$i - 1] * $tokens[$i + 1];
				array_splice($tokens, $i - 1, 3, $result);
				$i -= 2;
			}
		}

		// Затем сложение и вычитание
		$result = $tokens[0];
		for ($i = 1; $i < count($tokens); $i += 2) {
			$operator = $tokens[$i];
			$number = $tokens[$i + 1];

			switch ($operator) {
				case '+':
					$result += $number;
					break;
				case '-':
					$result -= $number;
					break;
			}
		}

		return $result;
	}
}
