<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$postId = $data['id'];

// Создаем белый список разрешенных HTML-тегов
$allowedTags = '<b><i><u><s><sup><sub><h2><ul><li><a><p><br><img><pre><span><div><iframe><audio><source><center>';

// Обрабатываем переносы строк
$content = $data['content'];
$content = str_replace("\n", "<br>", $content);

// Формируем HTML для обновленной статьи
$articleHtml = sprintf(
    '<article class="post">
        <h2>%s</h2>
        <div class="content">%s</div>
        <div class="date">%s (ред.)</div>
    </article>',
    htmlspecialchars($data['title']),
    strip_tags($content, $allowedTags),
    date('d.m.Y H:i')
);

// Читаем содержимое blog.html
$blogContent = file_get_contents('blog.html');

// Получаем все статьи
preg_match_all('/(<article class="post".*?<\/article>)/s', $blogContent, $matches);

if (isset($matches[1][$postId])) {
    // Заменяем старую статью на новую
    $matches[1][$postId] = $articleHtml;
    
    // Собираем новое содержимое
    $mainContent = implode("\n", $matches[1]);
    
    // Обновляем файл blog.html
    $newContent = preg_replace(
        '/(<main>).*?(<\/main>)/s',
        '$1' . $mainContent . '$2',
        $blogContent
    );
    
    file_put_contents('blog.html', $newContent);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Статья не найдена']);
}
