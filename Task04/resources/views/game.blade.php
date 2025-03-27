@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/game.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row justify-content-center">
	<div class="col-md-8">
		<div class="card shadow">
			<div class="card-header">
				<h3 class="card-title mb-0">Вычислите выражение</h3>
			</div>
			<div class="card-body p-4">
				<div class="expression-display bg-light rounded p-4 mb-4 text-center">
					<h2 class="display-3 text-primary">{{ $expression }}</h2>
				</div>

				<form method="POST" action="{{ route('game.store') }}">
					@csrf
					<input type="hidden" name="expression" value="{{ $expression }}">
					<input type="hidden" name="correct_answer" value="{{ $correctAnswer }}">

					<div class="mb-4">
						<label for="player_name" class="form-label">Ваше имя</label>
						<input type="text" class="form-control form-control-lg" id="player_name" name="player_name" required>
					</div>

					<div class="mb-4">
						<label for="user_answer" class="form-label">Ваш ответ</label>
						<input type="number" step="any" class="form-control form-control-lg" id="user_answer" name="user_answer" required>
					</div>

					<div class="d-grid">
						<button type="submit" class="btn btn-primary btn-lg">
							<i class="fas fa-check-circle me-2"></i>Проверить
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection