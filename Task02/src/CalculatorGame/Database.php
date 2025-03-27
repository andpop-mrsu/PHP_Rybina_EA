<?php

namespace CalculatorGame;

class Database
{
	private \PDO $pdo;

	public function __construct(string $dbPath)
	{
		$this->pdo = new \PDO("sqlite:$dbPath", null, null, [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
		]);
		$this->initializeDatabase();
	}

	private function initializeDatabase(): void
	{
		$this->pdo->exec("
            CREATE TABLE IF NOT EXISTS game_results (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                player_name TEXT NOT NULL,
                expression TEXT NOT NULL,
                correct_answer INTEGER NOT NULL,
                user_answer INTEGER NOT NULL,
                is_correct BOOLEAN NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            
            CREATE INDEX IF NOT EXISTS idx_player_name ON game_results(player_name);
            CREATE INDEX IF NOT EXISTS idx_created_at ON game_results(created_at);
        ");
	}

	public function saveGameResult(
		string $playerName,
		string $expression,
		int $correctAnswer,
		int $userAnswer,
		bool $isCorrect
	): void {
		try {
			$this->pdo->beginTransaction();

			$stmt = $this->pdo->prepare("
                INSERT INTO game_results 
                (player_name, expression, correct_answer, user_answer, is_correct)
                VALUES (?, ?, ?, ?, ?)
            ");

			$stmt->execute([
				$playerName,
				$expression,
				$correctAnswer,
				$userAnswer,
				$isCorrect ? 1 : 0
			]);

			$this->pdo->commit();

			error_log("Successfully saved: $expression");
		} catch (\PDOException $e) {
			$this->pdo->rollBack();
			error_log("Failed to save: " . $e->getMessage());
			throw $e;
		}
	}

	public function getAllGameResults(): array
	{
		$results = $this->pdo->query("
            SELECT 
                player_name, 
                expression, 
                correct_answer, 
                user_answer, 
                is_correct, 
                datetime(created_at, 'localtime') as created_at
            FROM game_results
            ORDER BY created_at DESC
            LIMIT 100
        ")->fetchAll();

		error_log("Retrieved " . count($results) . " records");
		return $results;
	}
}
