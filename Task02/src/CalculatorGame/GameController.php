<?php

namespace CalculatorGame;

class GameController
{
	private CalculatorGame $game;
	private Database $database;
	private string $currentExpression;
	private int $currentAnswer;

	public function __construct(Database $database)
	{
		$this->database = $database;
		$this->game = new CalculatorGame();

		session_start();

		if (empty($_SESSION['current_expression'])) {
			$this->generateNewExpression();
		} else {
			$this->currentExpression = $_SESSION['current_expression'];
			$this->currentAnswer = $_SESSION['current_answer'];
		}
	}

	public function generateNewExpression(): void
	{
		$result = $this->game->generateExpression();
		$this->currentExpression = $result['expression'];
		$this->currentAnswer = $result['correct_answer'];

		$_SESSION['current_expression'] = $this->currentExpression;
		$_SESSION['current_answer'] = $this->currentAnswer;

		error_log("Generated new expression: {$this->currentExpression}");
	}

	public function getCurrentExpression(): string
	{
		return $this->currentExpression;
	}

	public function checkAnswer(string $playerName, int $userAnswer): array
	{
		if (empty(trim($playerName))) {
			throw new \InvalidArgumentException("Player name cannot be empty");
		}

		$savedExpression = $this->currentExpression;
		$savedAnswer = $this->currentAnswer;
		$isCorrect = ($userAnswer === $savedAnswer);

		error_log("Saving result for: {$savedExpression}");

		$this->database->saveGameResult(
			$playerName,
			$savedExpression,
			$savedAnswer,
			$userAnswer,
			$isCorrect
		);

		$this->generateNewExpression();

		return [
			'is_correct' => $isCorrect,
			'correct_answer' => $savedAnswer,
			'answered_expression' => $savedExpression,
			'new_expression' => $this->currentExpression
		];
	}

	public function getAllResults(): array
	{
		return $this->database->getAllGameResults();
	}
}
