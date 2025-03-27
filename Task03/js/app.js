let currentGameId = null;
let currentExpression = '';
let currentPlayerName = '';

// Инициализация приложения
document.addEventListener('DOMContentLoaded', () => {
    loadGamesHistory();
});

// Переключение между вкладками
function switchTab(tabId) {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    document.querySelector(`.tab[onclick="switchTab('${tabId}')"]`).classList.add('active');
    document.getElementById(tabId).style.display = 'block';
}

// Начать новую игру
function startGame() {
    const playerName = document.getElementById('player-name').value.trim();
    if (!playerName) {
        alert('Пожалуйста, введите ваше имя');
        return;
    }
    
    currentPlayerName = playerName;
    
    fetch('/games', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ playerName })
    })
    .then(response => response.json())
    .then(data => {
        currentGameId = data.id;
        document.getElementById('player-form').style.display = 'none';
        document.getElementById('game-play').style.display = 'block';
        generateNewExpression();
        updateStats();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при создании игры');
    });
}

// Генерация нового выражения
function generateNewExpression() {
    const operators = ['+', '-', '*'];
    const numbers = [];
    
    // Генерируем 4 случайных числа от 1 до 20
    for (let i = 0; i < 4; i++) {
        numbers.push(Math.floor(Math.random() * 20) + 1);
    }
    
    // Генерируем 3 случайных оператора
    const ops = [];
    for (let i = 0; i < 3; i++) {
        ops.push(operators[Math.floor(Math.random() * operators.length)]);
    }
    
    // Собираем выражение
    currentExpression = `${numbers[0]}${ops[0]}${numbers[1]}${ops[1]}${numbers[2]}${ops[2]}${numbers[3]}`;
    document.getElementById('expression').textContent = currentExpression;
    document.getElementById('answer').value = '';
    document.getElementById('message').textContent = '';
    document.getElementById('answer').focus();
}

// Проверка ответа
function checkAnswer() {
    const answerInput = document.getElementById('answer');
    const userAnswer = parseFloat(answerInput.value);
    
    if (isNaN(userAnswer)) {
        alert('Пожалуйста, введите число');
        return;
    }
    
    // Вычисляем правильный ответ
    const correctAnswer = eval(currentExpression); // Внимание: eval может быть опасен, но в данном случае мы контролируем входные данные
    
    const isCorrect = Math.abs(userAnswer - correctAnswer) < 0.0001; // Учитываем возможные ошибки округления
    
    const message = document.getElementById('message');
    message.textContent = isCorrect 
        ? 'Правильно! Молодец!' 
        : `Неправильно. Правильный ответ: ${correctAnswer}`;
    message.className = isCorrect ? 'message correct' : 'message incorrect';
    
    // Отправляем результат на сервер
    fetch(`/step/${currentGameId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            expression: currentExpression,
            userAnswer: userAnswer,
            correctAnswer: correctAnswer,
            isCorrect: isCorrect
        })
    })
    .then(response => response.json())
    .then(() => {
        updateStats();
        setTimeout(generateNewExpression, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Обновление статистики текущей игры
function updateStats() {
    if (!currentGameId) return;
    
    fetch(`/games/${currentGameId}`)
    .then(response => response.json())
    .then(game => {
        document.getElementById('score').textContent = game.score || 0;
        
        // Обновляем вкладку статистики
        const statsDiv = document.getElementById('current-stats');
        if (game.steps && game.steps.length > 0) {
            let correct = 0;
            game.steps.forEach(step => {
                if (step.is_correct) correct++;
            });
            
            statsDiv.innerHTML = `
                <p>Игрок: ${game.player_name}</p>
                <p>Дата начала: ${new Date(game.start_time).toLocaleString()}</p>
                <p>Правильных ответов: ${correct} из ${game.steps.length}</p>
                <p>Счет: ${game.score}</p>
                
                <h3>Последние ответы:</h3>
                <ul>
                    ${game.steps.slice(-5).map(step => `
                        <li>${step.expression} = ${step.user_answer} 
                            (${step.is_correct ? '✓' : '✗'} Правильно: ${step.correct_answer})
                        </li>
                    `).join('')}
                </ul>
            `;
        } else {
            statsDiv.innerHTML = '<p>Еще нет данных об игре</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Загрузка истории всех игр
function loadGamesHistory() {
    fetch('/games')
    .then(response => response.json())
    .then(games => {
        const tbody = document.querySelector('#games-table tbody');
        tbody.innerHTML = games.map(game => `
            <tr onclick="showGameDetails(${game.id})" style="cursor: pointer;">
                <td>${game.id}</td>
                <td>${game.player_name}</td>
                <td>${new Date(game.start_time).toLocaleString()}</td>
                <td>${game.correct_answers || 0}</td>
                <td>${game.total_steps || 0}</td>
                <td>${game.score || 0}</td>
            </tr>
        `).join('');
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Показать детали конкретной игры
function showGameDetails(gameId) {
    fetch(`/games/${gameId}`)
    .then(response => response.json())
    .then(game => {
        const detailsDiv = document.getElementById('game-details');
        detailsDiv.style.display = 'block';
        
        const tbody = document.querySelector('#steps-table tbody');
        if (game.steps && game.steps.length > 0) {
            tbody.innerHTML = game.steps.map(step => `
                <tr>
                    <td>${step.expression}</td>
                    <td>${step.user_answer}</td>
                    <td>${step.correct_answer}</td>
                    <td>${step.is_correct ? '✓' : '✗'}</td>
                    <td>${new Date(step.time).toLocaleString()}</td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="5">Нет данных о шагах игры</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Обработка нажатия Enter в поле ответа
document.getElementById('answer').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        checkAnswer();
    }
});