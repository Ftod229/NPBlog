<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$postId = $data['id'];

// Читаем содержимое blog.html
$content = file_get_contents('blog.html');

// Получаем все статьи
preg_match_all('/(<article class="post".*?<\/article>)/s', $content, $matches);

if (isset($matches[1][$postId])) {
    $article = $matches[1][$postId];
    
    // Извлекаем заголовок
    preg_match('/<h2>(.*?)<\/h2>/s', $article, $titleMatch);
    
    // Извлекаем содержимое
    preg_match('/<div class="content">(.*?)<\/div>/s', $article, $contentMatch);
    
    // Заменяем <br> на переносы строк
    $content = str_replace('<br>', "\n", $contentMatch[1]);
    
    echo json_encode([
        'success' => true,
        'title' => strip_tags($titleMatch[1]),
        'content' => $content
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Статья не найдена']);
}