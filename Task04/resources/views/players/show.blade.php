@extends('layouts.app')

@section('content')
<div class="container">
	<h1>Игры игрока: {{ $player->name }}</h1>
	<table class="table">
		<thead>
			<tr>
				<th>Выражение</th>
				<th>Правильный ответ</th>
				<th>Ответ игрока</th>
				<th>Результат</th>
				<th>Дата</th>
			</tr>
		</thead>
		<tbody>
			@foreach($games as $game)
			<tr>
				<td>{{ $game->expression }}</td>
				<td>{{ $game->correct_answer }}</td>
				<td>{{ $game->user_answer }}</td>
				<td>
					@if($game->is_correct)
					<span class="text-success">✓</span>
					@else
					<span class="text-danger">✗</span>
					@endif
				</td>
				<td>{{ $game->created_at }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection