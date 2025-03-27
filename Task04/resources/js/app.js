document.addEventListener("DOMContentLoaded", function () {
    // Здесь будет логика вашего фронтенда
    console.log("Game app initialized");

    // Пример работы с API
    async function startNewGame(playerName) {
        try {
            const response = await fetch("/api/games", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    player_name: playerName,
                }),
            });

            return await response.json();
        } catch (error) {
            console.error("Error:", error);
        }
    }

    // Экспортируем функции для использования в других файлах
    window.GameApp = {
        startNewGame,
    };
});
