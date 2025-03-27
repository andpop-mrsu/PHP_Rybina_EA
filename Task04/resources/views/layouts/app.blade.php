<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Калькулятор - Laravel</title>
	<!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<!-- Шрифты -->
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Playfair+Display:wght@400;700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
	<!-- Ваши стили -->
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	@stack('styles')
</head>

<body class="bg-light">
	<nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
		<div class="container">
			<a class="navbar-brand" href="{{ route('game.play') }}">
				<i class="fas fa-calculator me-2"></i>Калькулятор
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class="nav-link" href="{{ route('games.index') }}">
							<i class="fas fa-history me-1"></i>История игр
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('players.index') }}">
							<i class="fas fa-users me-1"></i>Игроки
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<main class="container animate-fade">
		@yield('content')
	</main>

	<!-- Font Awesome для иконок -->
	<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	@stack('scripts')
</body>

</html>