<?php
header('Content-Type: application/json');

// Проверяем наличие папки uploads
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'Файл не был загружен']);
    exit;
}

$file = $_FILES['image'];
$fileName = $file['name'];
$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Проверяем тип файла
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Недопустимый тип файла']);
    exit;
}

// Генерируем уникальное имя файла
$newFileName = uniqid() . '.' . $fileType;
$uploadPath = 'uploads/' . $newFileName;

// Загружаем файл
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    echo json_encode([
        'success' => true,
        'url' => $uploadPath
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка при сохранении файла']);
}