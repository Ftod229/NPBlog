<?php
header('Content-Type: application/json');

// Получаем данные
$data = json_decode(file_get_contents('php://input'), true);

// Создаем белый список разрешенных HTML-тегов
$allowedTags = '<b><i><u><s><sup><sub><h2><ul><li><a><p><br><img><pre><span><div><iframe><audio><source><center>'; // Добавили тег img

// Обрабатываем переносы строк
$content = $data['content'];
$content = str_replace("\n", "<br>", $content);

// Формируем HTML для новой статьи
$articleHtml = sprintf(
    '<article class="post">
        <h2>%s</h2>
        <div class="content">%s</div>
        <div class="date">%s</div>
    </article>',
    htmlspecialchars($data['title']),
    strip_tags($content, $allowedTags), // Теперь разрешены img теги
    date('d.m.Y H:i')
);

// Читаем текущее содержимое blog.html
$blogContent = file_get_contents('blog.html');

// Находим место для вставки новой статьи (после открывающего main)
$insertPosition = strpos($blogContent, '<main>') + 6;

// Вставляем новую статью в начало списка
$newContent = substr_replace($blogContent, $articleHtml, $insertPosition, 0);

// Сохраняем обновленный файл
file_put_contents('blog.html', $newContent);

echo json_encode(['success' => true]);
?>
