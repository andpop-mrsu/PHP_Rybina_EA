<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Nyholm\Psr7\Factory\Psr17Factory;

// Фабрика ответа
$responseFactory = new Psr17Factory();
AppFactory::setResponseFactory($responseFactory);
$app = AppFactory::create();

// Middleware для обработки ошибок
$app->addErrorMiddleware(true, true, true);

// Подключение к БД
require __DIR__ . '/../src/Database.php';
$db = new Database();

/**
 * Преобразует данные в JSON-ответ
 */
function jsonResponse(Response $response, $data, int $status = 200): Response
{
	$json = json_encode($data);
	if ($json === false) {
		$json = json_encode(['error' => 'JSON encoding error']);
		$status = 500;
	}

	$response->getBody()->write($json);
	return $response
		->withStatus($status)
		->withHeader('Content-Type', 'application/json');
}

/**
 * Настройка маршрутов приложения
 */
function setupRoutes($app, $db)
{
	$app->get('/games', function (Request $request, Response $response) use ($db) {
		try {
			$games = $db->getAllGames();
			return jsonResponse($response, $games);
		} catch (Exception $e) {
			return jsonResponse($response, ['error' => $e->getMessage()], 500);
		}
	});

	$app->get('/games/{id}', function (Request $request, Response $response, array $args) use ($db) {
		try {
			$game = $db->getGameById($args['id']);
			if (!$game) {
				return jsonResponse($response, ['error' => 'Game not found'], 404);
			}
			return jsonResponse($response, $game);
		} catch (Exception $e) {
			return jsonResponse($response, ['error' => $e->getMessage()], 500);
		}
	});

	$app->post('/games', function (Request $request, Response $response) use ($db) {
		try {
			$data = json_decode($request->getBody()->getContents(), true);
			if (!isset($data['playerName'])) {
				return jsonResponse($response, ['error' => 'playerName is required'], 400);
			}

			$gameId = $db->createGame($data['playerName']);
			return jsonResponse($response, ['id' => $gameId], 201);
		} catch (Exception $e) {
			return jsonResponse($response, ['error' => $e->getMessage()], 500);
		}
	});

	$app->post('/step/{id}', function (Request $request, Response $response, array $args) use ($db) {
		try {
			$data = json_decode($request->getBody()->getContents(), true);
			$required = ['expression', 'userAnswer', 'correctAnswer', 'isCorrect'];

			foreach ($required as $field) {
				if (!isset($data[$field])) {
					return jsonResponse($response, ['error' => "Field $field is required"], 400);
				}
			}

			$success = $db->addStep(
				$args['id'],
				$data['expression'],
				$data['userAnswer'],
				$data['correctAnswer'],
				$data['isCorrect']
			);

			if (!$success) {
				return jsonResponse($response, ['error' => 'Failed to add step'], 400);
			}

			return jsonResponse($response, ['status' => 'success']);
		} catch (Exception $e) {
			return jsonResponse($response, ['error' => $e->getMessage()], 500);
		}
	});
}

// Маршрут для главной страницы (отдает HTML)
$app->get('/', function (Request $request, Response $response) {
	$html = file_get_contents(__DIR__ . '/index.html');
	$response->getBody()->write($html);
	return $response->withHeader('Content-Type', 'text/html');
});

$app->get('/js/{file}', function (Request $request, Response $response, array $args) {
	$file = __DIR__ . '/../js/' . $args['file'];
	if (file_exists($file)) {
		$response->getBody()->write(file_get_contents($file));
		return $response->withHeader('Content-Type', 'application/javascript');
	}
	return $response->withStatus(404);
});

setupRoutes($app, $db);
$app->run();
