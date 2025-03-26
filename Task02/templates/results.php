<h2>Результаты игр</h2>
<a href="?action=new">Вернуться к игре</a>

<table>
	<thead>
		<tr>
			<th>Игрок</th>
			<th>Выражение</th>
			<th>Правильный ответ</th>
			<th>Ответ игрока</th>
			<th>Результат</th>
			<th>Дата</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($results as $result): ?>
			<tr>
				<td><?= htmlspecialchars($result['player_name']) ?></td>
				<td><?= htmlspecialchars($result['expression']) ?></td>
				<td><?= $result['correct_answer'] ?></td>
				<td><?= $result['user_answer'] ?></td>
				<td><?= $result['is_correct'] ? '✓' : '✗' ?></td>
				<td><?= $result['created_at'] ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>