<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use CalculatorGame\Database;
use CalculatorGame\GameController;

try {
	// Инициализация базы данных
	if (!file_exists(__DIR__ . '/../db')) {
		mkdir(__DIR__ . '/../db', 0755, true);
	}
	$dbPath = __DIR__ . '/../db/database.sqlite';
	$database = new Database($dbPath);
	$controller = new GameController($database);

	// Обработка действий
	$action = $_GET['action'] ?? 'new';
	$templateData = [];

	switch ($action) {
		case 'check':
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$playerName = trim($_POST['player_name'] ?? 'Гость');
				$userAnswer = (int)($_POST['user_answer'] ?? 0);

				// Сохраняем текущее выражение ДО обработки ответа
				$currentExpression = $controller->getCurrentExpression();

				$result = $controller->checkAnswer($playerName, $userAnswer);

				$templateData = [
					'expression' => $currentExpression, // Используем сохраненное выражение
					'result' => [
						'is_correct' => $result['is_correct'],
						'correct_answer' => $result['correct_answer']
					],
					'player_name' => $playerName,
					'new_expression' => $result['new_expression'] // Новое выражение для информации
				];
				$template = 'game.php';
			}
			break;

		case 'results':
			$templateData = ['results' => $controller->getAllResults()];
			$template = 'results.php';
			break;

		case 'new':
		default:
			$templateData = [
				'expression' => $controller->getCurrentExpression(),
				'player_name' => $_POST['player_name'] ?? 'Гость'
			];
			$template = 'game.php';
			break;
	}

	function renderTemplate(string $template, array $data = []): string
	{
		extract($data);
		ob_start();
		include __DIR__ . '/../templates/' . $template;
		return ob_get_clean();
	}

	$content = renderTemplate($template, $templateData);
	include __DIR__ . '/../templates/layout.php';
} catch (Throwable $e) {
	error_log($e->getMessage());
	header("HTTP/1.1 500 Internal Server Error");
	echo "Произошла ошибка. Пожалуйста, попробуйте позже.";
	echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
