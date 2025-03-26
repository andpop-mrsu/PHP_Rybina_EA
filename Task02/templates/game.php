<form method="post" action="?action=check">
	<div class="expression">Вычислите: <?= htmlspecialchars($expression) ?></div>

	<div class="form-group">
		<label for="player_name">Ваше имя:</label>
		<input type="text" id="player_name" name="player_name"
			value="<?= htmlspecialchars($player_name ?? 'Гость') ?>" required>
	</div>

	<div class="form-group">
		<label for="user_answer">Ваш ответ:</label>
		<input type="number" id="user_answer" name="user_answer" required>
	</div>

	<?php if (isset($result)): ?>
		<div class="result-container">
			<div class="result <?= $result['is_correct'] ? 'correct' : 'incorrect' ?>">
				<?= $result['is_correct'] ? '✅ Правильно!' : '❌ Неправильно!' ?>
				<div>Правильный ответ: <?= $result['correct_answer'] ?></div>
			</div>

			<div class="auto-refresh-message"
				data-refresh-url="?action=new"
				data-delay="5">
				Автоматическая смена через <span class="countdown">5</span> сек
			</div>

			<a href="?action=new" class="btn">Сразу новое выражение</a>
		</div>

		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const refreshElement = document.querySelector('.auto-refresh-message');
				if (refreshElement) {
					const delay = parseInt(refreshElement.dataset.delay);
					const url = refreshElement.dataset.refreshUrl;
					let remaining = delay;

					const interval = setInterval(() => {
						remaining--;
						refreshElement.querySelector('.countdown').textContent = remaining;

						if (remaining <= 0) {
							clearInterval(interval);
							window.location.href = url;
						}
					}, 1000);
				}
			});
		</script>
	<?php endif; ?>

	<button type="submit">Проверить</button>
</form>

<div class="links">
	<a href="?action=results">Посмотреть результаты</a>
</div>