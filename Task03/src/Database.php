<?php
class Database
{
	private $pdo;

	public function __construct()
	{
		$dbDir = __DIR__ . '/../db';
		$dbPath = $dbDir . '/database.db';

		// Создаем папку если не существует
		if (!file_exists($dbDir)) {
			if (!mkdir($dbDir, 0777, true)) {
				throw new RuntimeException("Не удалось создать директорию для базы данных");
			}
		}

		try {
			$this->pdo = new PDO("sqlite:$dbPath", null, null, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			]);

			// Включаем поддержку внешних ключей
			$this->pdo->exec("PRAGMA foreign_keys = ON");

			$this->initializeDatabase();
		} catch (PDOException $e) {
			throw new RuntimeException("Ошибка подключения к базе данных: " . $e->getMessage());
		}
	}

	private function initializeDatabase()
	{
		try {
			$this->pdo->exec("
                CREATE TABLE IF NOT EXISTS games (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    player_name TEXT NOT NULL,
                    start_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                    score INTEGER DEFAULT 0
                )
            ");

			$this->pdo->exec("
                CREATE TABLE IF NOT EXISTS steps (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    game_id INTEGER NOT NULL,
                    expression TEXT NOT NULL,
                    user_answer REAL NOT NULL,
                    correct_answer REAL NOT NULL,
                    is_correct BOOLEAN NOT NULL,
                    time DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
                )
            ");
		} catch (PDOException $e) {
			throw new RuntimeException("Ошибка инициализации базы данных: " . $e->getMessage());
		}
	}

	public function getAllGames()
	{
		try {
			$stmt = $this->pdo->query("
                SELECT g.id, g.player_name, g.start_time, 
                       COUNT(s.id) as total_steps,
                       SUM(CASE WHEN s.is_correct THEN 1 ELSE 0 END) as correct_answers,
                       g.score
                FROM games g
                LEFT JOIN steps s ON g.id = s.game_id
                GROUP BY g.id
                ORDER BY g.start_time DESC
            ");
			return $stmt->fetchAll();
		} catch (PDOException $e) {
			throw new RuntimeException("Ошибка получения списка игр: " . $e->getMessage());
		}
	}

	public function getGameById($id)
	{
		try {
			$stmt = $this->pdo->prepare("
                SELECT g.id, g.player_name, g.start_time, g.score
                FROM games g
                WHERE g.id = ?
            ");
			$stmt->execute([$id]);
			$game = $stmt->fetch();

			if ($game) {
				$stmt = $this->pdo->prepare("
                    SELECT expression, user_answer, correct_answer, is_correct, time
                    FROM steps
                    WHERE game_id = ?
                    ORDER BY time
                ");
				$stmt->execute([$id]);
				$game['steps'] = $stmt->fetchAll();
			}

			return $game;
		} catch (PDOException $e) {
			throw new RuntimeException("Ошибка получения информации об игре: " . $e->getMessage());
		}
	}

	public function createGame($playerName)
	{
		try {
			$stmt = $this->pdo->prepare("INSERT INTO games (player_name) VALUES (?)");
			$stmt->execute([$playerName]);
			return $this->pdo->lastInsertId();
		} catch (PDOException $e) {
			throw new RuntimeException("Ошибка создания новой игры: " . $e->getMessage());
		}
	}

	public function addStep($gameId, $expression, $userAnswer, $correctAnswer, $isCorrect)
	{
		try {
			$this->pdo->beginTransaction();

			$stmt = $this->pdo->prepare("
                INSERT INTO steps (game_id, expression, user_answer, correct_answer, is_correct)
                VALUES (?, ?, ?, ?, ?)
            ");
			$stmt->execute([$gameId, $expression, $userAnswer, $correctAnswer, $isCorrect ? 1 : 0]);

			if ($isCorrect) {
				$stmt = $this->pdo->prepare("
                    UPDATE games SET score = score + 1 WHERE id = ?
                ");
				$stmt->execute([$gameId]);
			}

			$this->pdo->commit();
			return true;
		} catch (Exception $e) {
			$this->pdo->rollBack();
			throw new RuntimeException("Ошибка добавления шага: " . $e->getMessage());
		}
	}
}
