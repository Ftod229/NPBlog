<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$postId = $data['id'];

// Читаем содержимое blog.html
$content = file_get_contents('blog.html');

// Получаем все статьи
preg_match_all('/(<article class="post".*?<\/article>)/s', $content, $matches);

if (isset($matches[1][$postId])) {
    // Удаляем статью из массива
    $articles = $matches[1];
    unset($articles[$postId]);
    
    // Собираем новое содержимое
    $mainContent = implode("\n", $articles);
    
    // Обновляем файл blog.html
    $newContent = preg_replace(
        '/(<main>).*?(<\/main>)/s',
        '$1' . $mainContent . '$2',
        $content
    );
    
    file_put_contents('blog.html', $newContent);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Статья не найдена']);
}