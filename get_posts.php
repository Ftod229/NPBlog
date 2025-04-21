<?php
// Читаем содержимое blog.html
$content = file_get_contents('blog.html');

// Создаем массив для хранения статей
$posts = [];

// Используем регулярное выражение для извлечения статей
preg_match_all('/<article class="post".*?>(.*?)<\/article>/s', $content, $matches);

foreach ($matches[1] as $index => $post) {
    // Извлекаем заголовок
    preg_match('/<h2>(.*?)<\/h2>/s', $post, $titleMatch);
    
    // Извлекаем дату
    preg_match('/<div class="date">(.*?)<\/div>/s', $post, $dateMatch);
    
    $posts[] = [
        'id' => $index, // Используем индекс как ID
        'title' => strip_tags($titleMatch[1]),
        'date' => strip_tags($dateMatch[1])
    ];
}

header('Content-Type: application/json');
echo json_encode(array_reverse($posts)); // Возвращаем статьи в обратном порядке