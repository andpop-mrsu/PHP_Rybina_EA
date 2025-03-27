@extends('layouts.app')

@section('content')
<div class="container">
	<h1>Все игроки</h1>
	<table class="table">
		<thead>
			<tr>
				<th>Имя</th>
				<th>Количество игр</th>
				<th>Дата регистрации</th>
				<th>Действия</th>
			</tr>
		</thead>
		<tbody>
			@foreach($players as $player)
			<tr>
				<td>{{ $player->name }}</td>
				<td>{{ $player->games_count }}</td>
				<td>{{ $player->created_at }}</td>
				<td>
					<a href="{{ route('players.show', $player) }}" class="btn btn-sm btn-info">Подробнее</a>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection