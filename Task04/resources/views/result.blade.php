@extends('layouts.app')

@section('content')
<div class="container">
	<h1>Результат</h1>
	<div class="card">
		<div class="card-body">
			<h2 class="card-title">
				@if($game->is_correct)
				<span class="text-success">Правильно!</span>
				@else
				<span class="text-danger">Неправильно!</span>
				@endif
			</h2>

			<p>Выражение: {{ $game->expression }}</p>
			<p>Ваш ответ: {{ $game->user_answer }}</p>
			<p>Правильный ответ: {{ $game->correct_answer }}</p>

			<a href="{{ route('game.play') }}" class="btn btn-primary">Играть снова</a>
			<a href="{{ route('games.index') }}" class="btn btn-secondary">Посмотреть все игры</a>
		</div>
	</div>
</div>
@endsection