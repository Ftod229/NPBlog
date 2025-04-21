<!DOCTYPE html>
<html>
<head>
    <title>Редактор</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        textarea {
            min-height: 200px;
            padding: 10px;
            line-height: 1.6;
            font-size: 14px;
            font-family: Arial, sans-serif;
            white-space: pre-wrap;
            resize: vertical;
        }
        .formatting-buttons {
            display: flex;
            gap: 5px;
            margin-bottom: 10px;
        }
        .format-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            background: #f5f5f5;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .format-btn:hover {
            background: #e5e5e5;
        }

        .manage-posts {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 300px;
            background: white;
            border-left: 1px solid #ddd;
            padding: 20px;
            overflow-y: auto;
            transform: translateX(100%);
            transition: transform 0.3s;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        }
        
        .manage-posts.active {
            transform: translateX(0);
        }
        
        .post-list {
            list-style: none;
            padding: 0;
        }
        
        .post-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        
        .delete-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .delete-btn:hover {
            background: #ff0000;
        }
        
        .manage-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .manage-btn:hover {
            background: #45a049;
        }
        
        .close-manage {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .dialog-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 5px;
            min-width: 300px;
        }

        .image-size-controls {
            margin: 15px 0;
        }

        .dialog-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
        }

        #customSizeInputs {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        #customSizeInputs input {
            width: 100px;
        }

        .edit-btn {
            position: absolute;
            right: 80px;
            top: 50%;
            transform: translateY(-50%);
            background: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .edit-btn:hover {
            background: #45a049;
        }

        #submitButton {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
        }

        #submitButton.editing {
            background: #ff9800;
        }
        .code-block {
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px;
        margin: 15px 0;
        position: relative;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 14px;
        line-height: 1.4;
        max-height: 400px;
        overflow-x: auto;
        white-space: pre;
    }

    .code-block::before {
        content: attr(data-language);
        position: absolute;
        top: -12px;
        right: 10px;
        background: #fff;
        padding: 0 5px;
        font-size: 12px;
        color: #666;
        border: 1px solid #ddd;
        border-radius: 3px;
    }

    .dialog.code-dialog .dialog-content {
        width: 600px;
        max-width: 90vw;
    }

    .code-input {
        width: 100%;
        min-height: 200px;
        font-family: 'Consolas', 'Monaco', monospace;
        margin: 10px 0;
        padding: 10px;
    }

    .language-select {
        width: 200px;
        padding: 5px;
        margin-bottom: 10px;
    }
    .font-size-select {
        width: auto;
        padding: 0 5px;
        font-size: 14px;
    }

    .font-family-select {
    width: auto; /* Автоматическая ширина */
    padding: 0 5px; /* Отступы внутри элемента */
    font-size: 14px; /* Размер шрифта */
    border: 1px solid #ccc; /* Граница серого цвета */
    border-radius: 4px; /* Скругленные углы */
    background-color: #fff; /* Белый фон */
    color: #333; /* Темно-серый цвет текста */
    cursor: pointer; /* Изменение курсора на "pointer" при наведении */
    outline: none; /* Убираем фокусное обрамление */
    transition: border-color 0.2s ease, box-shadow 0.2s ease; /* Плавный переход эффектов */
}

.font-family-select:focus, .font-family-select:hover {
    border-color: #66afe9; /* Изменение цвета границы при фокусе или наведении */
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6); /* Добавление тени */
}

/* Стиль для диалогового окна */
#fontFamilyDialog {
    display: none; /* Скрыто по умолчанию */
    position: fixed; /* Фиксированное положение */
    top: 50%; /* Центрирование по вертикали */
    left: 50%; /* Центрирование по горизонтали */
    transform: translate(-50%, -50%); /* Точное центрирование */
    background: #fff; /* Белый фон */
    padding: 20px; /* Внутренние отступы */
    border: 1px solid #ccc; /* Граница серого цвета */
    border-radius: 8px; /* Скругленные углы */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Небольшая тень */
    z-index: 1000; /* Выше других элементов */
}

#fontFamilyDialog label {
    display: block; /* Блоковый элемент */
    margin-bottom: 8px; /* Отступ снизу */
    font-size: 14px; /* Размер шрифта */
    color: #333; /* Темно-серый цвет текста */
}

#fontFamilyDialog input[type="text"] {
    width: 100%; /* Занимает всю доступную ширину */
    padding: 8px; /* Внутренние отступы */
    font-size: 14px; /* Размер шрифта */
    border: 1px solid #ccc; /* Граница серого цвета */
    border-radius: 4px; /* Скругленные углы */
    outline: none; /* Убираем фокусное обрамление */
    transition: border-color 0.2s ease, box-shadow 0.2s ease; /* Плавный переход эффектов */
}

#fontFamilyDialog input[type="text"]:focus {
    border-color: #66afe9; /* Изменение цвета границы при фокусе */
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6); /* Добавление тени */
}

#fontFamilyDialog button {
    margin-top: 10px; /* Отступ сверху */
    padding: 8px 16px; /* Внутренние отступы */
    font-size: 14px; /* Размер шрифта */
    border: none; /* Без границы */
    border-radius: 4px; /* Скругленные углы */
    background-color: #007bff; /* Синий фон */
    color: #fff; /* Белый цвет текста */
    cursor: pointer; /* Изменение курсора на "pointer" */
    transition: background-color 0.2s ease; /* Плавный переход цвета фона */
}

#fontFamilyDialog button:hover {
    background-color: #0056b3; /* Темно-синий фон при наведении */
}

    .font-size-span {
        display: inline-block;
        padding: 2px 5px;
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 3px;
        margin: 2px;
        font-size: inherit;
    }
    .size-input-group {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 5px;
    }

    .size-input-group input {
        width: 100px;
    }

    .size-input-group select {
        width: 60px;
    }

    #customSizeInputs {
        margin-top: 10px;
        display: none;
    }
    .media-select {
        width: 100%;
        padding: 5px;
        margin-bottom: 10px;
    }

    .media-input {
        width: 100%;
        padding: 5px;
        margin-bottom: 15px;
    }

    .media-container {
        position: relative;
        margin: 15px 0;
        width: 100%;
    }

    .media-container iframe {
        border: none;
        max-width: 100%;
    }

    .size-input-group label {
        display: inline-block;
        width: 70px;
    }
    .image-source-toggle {
        margin-bottom: 15px;
        display: flex;
        gap: 20px;
    }

    .image-source-toggle label {
        cursor: pointer;
    }

    .image-url-input {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #fileUploadContainer,
    #urlContainer {
        margin-bottom: 15px;
    }
    .color-picker {
        width: 30px;
        height: 30px;
        padding: 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
    }

    .color-picker::-webkit-color-swatch-wrapper {
        padding: 0;
    }

    .color-picker::-webkit-color-swatch {
        border: none;
        border-radius: 3px;
    }
    .format-btn sup, .format-btn sub {
        font-size: 0.7em;
        line-height: 1;
    }

    .format-btn sup {
        vertical-align: super;
    }

    .format-btn sub {
        vertical-align: sub;
    }

    iframe {
        border: 0;
    }
    </style>
</head>
<body>
    <h1>Редактор</h1>
    <form id="blogForm">
        <input type="text" id="title" placeholder="Заголовок статьи" required>
        <div class="formatting-buttons">
            <button type="button" class="format-btn" onclick="formatText('b')" title="Жирный">B</button>
            <button type="button" class="format-btn" onclick="formatText('i')" title="Курсив"><i>I</i></button>
            <button type="button" class="format-btn" onclick="formatText('u')" title="Подчеркнутый">U</button>
            <button type="button" class="format-btn" onclick="formatText('s')" title="Зачеркнутый"><s>S</s></button>
            <button type="button" class="format-btn" onclick="formatText('sup')" title="Верхний индекс">X<sup>2</sup></button>
            <button type="button" class="format-btn" onclick="formatText('sub')" title="Нижний индекс">X<sub>2</sub></button>
            <button type="button" class="format-btn" onclick="formatText('h2')" title="Подзаголовок">H</button>
            <button type="button" class="format-btn" onclick="formatText('center')" title="По центру">►◄</button>
            <button type="button" class="format-btn" onclick="formatText('p')" title="По правому краю">►</button>
            <button type="button" class="format-btn" onclick="insertList()" title="Список">•</button>
            <button type="button" class="format-btn" onclick="addLink()" title="Ссылка">🔗</button>
            <button type="button" class="format-btn" onclick="showImageUpload()" title="Добавить изображение">📷</button>
            <button type="button" class="format-btn" onclick="showMediaDialog()" title="Добавить медиа">🎬</button>
            <button type="button" class="format-btn" onclick="insertCode()" title="Вставить код">{}</button>
            <select class="format-btn font-size-select" onchange="setFontSize(this.value)" title="Размер шрифта">
            <option value="">Размер</option>
            <option value="12">12px</option>
            <option value="14">14px</option>
            <option value="16">16px</option>
            <option value="18">18px</option>
            <option value="20">20px</option>
            <option value="24">24px</option>
            <option value="28">28px</option>
            <option value="32">32px</option>
            <option value="custom">Свой</option>
            </select>
            <select class="format-btn font-family-select" onchange="setFontFamily(this.value)" title="Шрифт">
            <option value="">Выберите шрифт</option>
            <option value="Arial">Arial</option>
            <option value="Times New Roman">Times New Roman</option>
            <option value="Open Sans">Open Sans</option>
            <option value="Verdana">Verdana</option>
            <option value="Helvetica">Helvetica</option>
            <option value="Georgia">Georgia</option>
            <option value="PT Sans">PT Sans</option>
            </select>
            <input type="color" id="textColor" class="color-picker" onchange="setTextColor(this.value)" title="Цвет текста">
            </select>
            <a href="blog.html" download id="downloadLink"><button type="button" class="format-btn" title="Скачать blog.html">↓</button></a>
            <a href="ftp.php"><button type="button" class="format-btn" title="Скачать blog.html">ftp</button></a>
        </div>
        <textarea id="content" placeholder="Содержание статьи" required></textarea>
        <button type="submit" id="submitButton">Опубликовать</button>
        ver 2.19 <iframe src="https://ftod.w10.site/update.html" width="600" height="400"></iframe>
    </form>

    <button type="button" class="manage-btn" onclick="toggleManagePosts()">Управление статьями</button>

    <div class="manage-posts" id="managePosts">
        <button class="close-manage" onclick="toggleManagePosts()">×</button>
        <h2>Все статьи</h2>
        <div id="postsList"></div>
    </div>
    
    <div id="imageUploadDialog" class="dialog">
    <div class="dialog-content">
        <h3>Добавить изображение</h3>
        
        <!-- Добавляем переключатель -->
        <div class="image-source-toggle">
            <label>
                <input type="radio" name="imageSource" value="file" checked> Загрузить файл
            </label>
            <label>
                <input type="radio" name="imageSource" value="url"> Вставить ссылку
            </label>
        </div>

        <!-- Контейнер для загрузки файла -->
        <div id="fileUploadContainer">
            <input type="file" id="imageFile" accept="image/*">
        </div>

        <!-- Контейнер для ссылки -->
        <div id="urlContainer" style="display: none;">
            <input type="text" id="imageUrl" placeholder="Введите URL изображения" class="image-url-input">
        </div>

        <div class="image-size-controls">
            <label>
                Размер:
                <select id="imageSize">
                    <option value="small">Маленький</option>
                    <option value="medium" selected>Средний</option>
                    <option value="large">Большой</option>
                    <option value="custom">Свой размер</option>
                </select>
            </label>
            <div id="customSizeInputs" style="display: none;">
                <div class="size-input-group">
                    <input type="number" id="customWidth" placeholder="Ширина">
                    <select id="widthUnit">
                        <option value="px">px</option>
                        <option value="%">%</option>
                    </select>
                </div>
                <div class="size-input-group">
                    <input type="number" id="customHeight" placeholder="Высота">
                    <select id="heightUnit">
                        <option value="px">px</option>
                        <option value="%">%</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="dialog-buttons">
            <button onclick="processImage()">Добавить</button>
            <button onclick="closeImageDialog()">Отмена</button>
        </div>
    </div>
</div>

    <div id="codeDialog" class="dialog code-dialog">
    <div class="dialog-content">
        <h3>Вставить код</h3>
        <select id="codeLanguage" class="language-select">
            <option value="javascript">JavaScript</option>
            <option value="php">PHP</option>
            <option value="html">HTML</option>
            <option value="css">CSS</option>
            <option value="python">Python</option>
            <option value="sql">SQL</option>
            <option value="java">Java</option>
            <option value="cpp">C++</option>
            <option value="csharp">C#</option>
            <option value="ruby">Ruby</option>
            <option value="plain">Текст</option>
        </select>
        <textarea id="codeInput" class="code-input" placeholder="Вставьте ваш код сюда..."></textarea>
        <div class="dialog-buttons">
            <button onclick="insertCodeBlock()">Вставить</button>
            <button onclick="closeCodeDialog()">Отмена</button>
        </div>
    </div>
</div>

<div id="fontSizeDialog" class="dialog">
    <div class="dialog-content">
        <h3>Указать размер шрифта</h3>
        <input type="number" id="customFontSize" min="8" max="72" placeholder="Размер в px">
        <div class="dialog-buttons">
            <button onclick="setCustomFontSize()">Применить</button>
            <button onclick="closeFontSizeDialog()">Отмена</button>
        </div>
    </div>
</div>


<div id="mediaDialog" class="dialog">
    <div class="dialog-content">
        <h3>Добавить медиа</h3>
        <input type="text" id="mediaUrl" placeholder="Вставьте ссылку на YouTube, Vimeo или аудио файл" class="media-input">
        <div class="dialog-buttons">
            <button onclick="insertMedia()">Вставить</button>
            <button onclick="closeMediaDialog()">Отмена</button>
        </div>
    </div>
</div>

<script>
    let currentEditId = null;

    function formatText(tag) {
        const textarea = document.getElementById('content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        const beforeText = textarea.value.substring(0, start);
        const afterText = textarea.value.substring(end);

        let formattedText;
        if (tag === 'h2') {
            formattedText = `<${tag}>${selectedText}</${tag}>\n`;
        } else {
            formattedText = `<${tag}>${selectedText}</${tag}>`;
        }

        textarea.value = beforeText + formattedText + afterText;

        textarea.setSelectionRange(start + tag.length + 2, start + tag.length + 2 + selectedText.length);
    }

    function insertList() {
        const textarea = document.getElementById('content');
        const listTemplate = "\n<ul>\n  <li>Пункт 1</li>\n  <li>Пункт 2</li>\n  <li>Пункт 3</li>\n</ul>\n";
        const cursorPos = textarea.selectionStart;
        textarea.value = textarea.value.substring(0, cursorPos) + listTemplate + textarea.value.substring(cursorPos);
        textarea.focus();
    }

    function addLink() {
        const textarea = document.getElementById('content');
        const url = prompt('Введите URL:', 'https://');
        if (url) {
            const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
            const linkText = selectedText || 'ссылка';
            const link = `<a href="${url}">${linkText}</a>`;
            
            const start = textarea.selectionStart;
            textarea.value = textarea.value.substring(0, start) + 
                           link + 
                           textarea.value.substring(textarea.selectionEnd);
        }
    }

    // Функции для работы с изображениями
    function showImageUpload() {
        document.getElementById('imageUploadDialog').style.display = 'block';
    }

    document.querySelectorAll('input[name="imageSource"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('fileUploadContainer').style.display = 
                this.value === 'file' ? 'block' : 'none';
            document.getElementById('urlContainer').style.display = 
                this.value === 'url' ? 'block' : 'none';
        });
    });

    function processImage() {
        const imageSource = document.querySelector('input[name="imageSource"]:checked').value;
        const sizeSelect = document.getElementById('imageSize');
        const sizeValue = sizeSelect.value;
        
        let width, height, widthUnit, heightUnit;
        
        if (sizeValue === 'custom') {
            width = document.getElementById('customWidth').value;
            height = document.getElementById('customHeight').value;
            widthUnit = document.getElementById('widthUnit').value;
            heightUnit = document.getElementById('heightUnit').value;
            
            if (width && (widthUnit === '%' && (width < 1 || width > 100))) {
                alert('Процентное значение ширины должно быть от 1 до 100');
                return;
            }
            if (height && (heightUnit === '%' && (height < 1 || height > 100))) {
                alert('Процентное значение высоты должно быть от 1 до 100');
                return;
            }
        } else {
            const sizes = {
                small: { width: 300 },
                medium: { width: 500 },
                large: { width: 800 }
            };
            width = sizes[sizeValue].width;
            widthUnit = 'px';
        }

        if (imageSource === 'url') {
            const imageUrl = document.getElementById('imageUrl').value.trim();
            if (!imageUrl) {
                alert('Пожалуйста, введите URL изображения');
                return;
            }
            insertImage(imageUrl, width, height, widthUnit, heightUnit);
        } else {
            const file = document.getElementById('imageFile').files[0];
            if (!file) {
                alert('Пожалуйста, выберите файл');
                return;
            }
            uploadImage(file, width, height, widthUnit, heightUnit);
        }
    }

    function uploadImage(file, width, height, widthUnit, heightUnit) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('width', width);
        formData.append('height', height || '');
        formData.append('widthUnit', widthUnit);
        formData.append('heightUnit', heightUnit || '');

        fetch('upload_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                insertImage(data.url, width, height, widthUnit, heightUnit);
            } else {
                alert('Ошибка при загрузке изображения: ' + data.error);
            }
        })
        .catch(error => {
            alert('Ошибка при загрузке изображения');
        });
    }

    function insertImage(url, width, height, widthUnit, heightUnit) {
        const imgStyle = `width: ${width}${widthUnit}; ` + 
                        (height ? `height: ${height}${heightUnit};` : '');
        
        const imgTag = `<img src="${url}" style="${imgStyle}" class="blog-image">`;
        const textarea = document.getElementById('content');
        const cursorPos = textarea.selectionStart;
        textarea.value = textarea.value.substring(0, cursorPos) + 
                        imgTag + 
                        textarea.value.substring(cursorPos);
        closeImageDialog();
    }

    function closeImageDialog() {
        document.getElementById('imageUploadDialog').style.display = 'none';
        document.getElementById('imageFile').value = '';
        document.getElementById('imageUrl').value = '';
        document.getElementById('customWidth').value = '';
        document.getElementById('customHeight').value = '';
        document.querySelector('input[name="imageSource"][value="file"]').checked = true;
        document.getElementById('fileUploadContainer').style.display = 'block';
        document.getElementById('urlContainer').style.display = 'none';
    }

    // Функции для работы с размером шрифта
    function setFontSize(size) {
        if (size === 'custom') {
            document.getElementById('fontSizeDialog').style.display = 'block';
            document.getElementById('customFontSize').focus();
            return;
        }
        
        const textarea = document.getElementById('content');
        const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
        
        if (selectedText) {
            const fontSpan = `<span style="font-size: ${size}px;">${selectedText}</span>`;
            const start = textarea.selectionStart;
            textarea.value = textarea.value.substring(0, start) + 
                           fontSpan + 
                           textarea.value.substring(textarea.selectionEnd);
        }
        
        document.querySelector('.font-size-select').value = '';
    }

    function closeFontSizeDialog() {
        document.getElementById('fontSizeDialog').style.display = 'none';
        document.getElementById('customFontSize').value = '';
    }

    function setCustomFontSize() {
        const size = document.getElementById('customFontSize').value;
        if (size && size >= 8 && size <= 72) {
            setFontSize(size);
            closeFontSizeDialog();
        } else {
            alert('Пожалуйста, введите размер от 8 до 72 пикселей');
        }
    }

    // Функции для работы с медиа
    function showMediaDialog() {
        document.getElementById('mediaDialog').style.display = 'block';
    }

    function closeMediaDialog() {
        document.getElementById('mediaDialog').style.display = 'none';
        document.getElementById('mediaUrl').value = '';
    }

    function insertMedia() {
    const url = document.getElementById('mediaUrl').value.trim();
    if (!url) {
        alert('Пожалуйста, введите URL');
        return;
    }

    let embedCode = '';

    // Определяем тип медиа по URL
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
        const youtubeId = extractYoutubeId(url);
        embedCode = `<iframe width="560" height="315" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
    } else if (url.includes('vimeo.com')) {
        const vimeoId = extractVimeoId(url);
        embedCode = `<iframe width="560" height="315" src="https://player.vimeo.com/video/${vimeoId}" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
    } else if (url.match(/\.(mp3|wav|ogg)$/i)) {
        embedCode = `<audio controls><source src="${url}">Ваш браузер не поддерживает аудио элемент.</audio>`;
    } else {
        embedCode = `<iframe width="560" height="315" src="${url}" frameborder="0" allowfullscreen></iframe>`;
    }

    const textarea = document.getElementById('content');
    const cursorPos = textarea.selectionStart;
    textarea.value = textarea.value.substring(0, cursorPos) +
                    embedCode +
                    textarea.value.substring(cursorPos);

    closeMediaDialog();
}

// Вспомогательные функции для извлечения ID
function extractYoutubeId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

function extractVimeoId(url) {
    const regExp = /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/;
    const match = url.match(regExp);
    return match ? match[3] : null;
}

    // Функции для работы с кодом
    function insertCode() {
        document.getElementById('codeDialog').style.display = 'block';
    }

    function closeCodeDialog() {
        document.getElementById('codeDialog').style.display = 'none';
        document.getElementById('codeInput').value = '';
    }

    function insertCodeBlock() {
        const code = document.getElementById('codeInput').value;
        const language = document.getElementById('codeLanguage').value;
        
        if (code.trim() === '') {
            alert('Пожалуйста, введите код');
            return;
        }

        const escapedCode = code
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        const codeBlock = `<pre class="code-block" data-language="${language}">${escapedCode}</pre>`;
        
        const textarea = document.getElementById('content');
        const cursorPos = textarea.selectionStart;
        textarea.value = textarea.value.substring(0, cursorPos) + 
                        codeBlock + 
                        textarea.value.substring(cursorPos);
        
        closeCodeDialog();
    }

    // Функции для управления статьями
    function toggleManagePosts() {
        const managePanel = document.getElementById('managePosts');
        managePanel.classList.toggle('active');
        
        if (managePanel.classList.contains('active')) {
            loadPosts();
        }
    }

    function loadPosts() {
        fetch('get_posts.php')
            .then(response => response.json())
            .then(posts => {
                const postsList = document.getElementById('postsList');
                postsList.innerHTML = '<ul class="post-list">' +
                    posts.map(post => `
                        <li class="post-item">
                            <div>${post.title}</div>
                            <small>${post.date}</small>
                            <button class="edit-btn" onclick="editPost('${post.id}')">Edit</button>
                            <button class="delete-btn" onclick="deletePost('${post.id}')">Delete</button>
                        </li>
                    `).join('') +
                    '</ul>';
            });
    }

    function editPost(postId) {
        fetch('get_post_content.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('title').value = data.title;
                document.getElementById('content').value = data.content;
                currentEditId = postId;
                
                const submitButton = document.getElementById('submitButton');
                submitButton.textContent = 'Сохранить изменения';
                submitButton.classList.add('editing');
                
                toggleManagePosts();
                document.getElementById('blogForm').scrollIntoView();
            }
        });
    }

    function deletePost(postId) {
        if (confirm('Вы уверены, что хотите удалить эту статью?')) {
            fetch('delete_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadPosts();
                } else {
                    alert('Ошибка при удалении статьи');
                }
            });
        }
    }

    // Обработчик отправки формы
    document.getElementById('blogForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const title = document.getElementById('title').value;
        const content = document.getElementById('content').value;
        
        const endpoint = currentEditId ? 'update_post.php' : 'save_post.php';
        const data = {
            title: title,
            content: content
        };
        
        if (currentEditId) {
            data.id = currentEditId;
        }
        
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.text())
        .then(data => {
            alert(currentEditId ? 'Статья успешно обновлена!' : 'Статья успешно добавлена!');
            document.getElementById('blogForm').reset();
            
            currentEditId = null;
            const submitButton = document.getElementById('submitButton');
            submitButton.textContent = 'Опубликовать';
            submitButton.classList.remove('editing');
        })
        .catch(error => {
            alert('Ошибка при сохранении статьи');
        });
    });

    // Обработчики изменения размера
    document.getElementById('imageSize').addEventListener('change', function(e) {
        const customInputs = document.getElementById('customSizeInputs');
        customInputs.style.display = e.target.value === 'custom' ? 'flex' : 'none';
        
        if (e.target.value !== 'custom') {
            document.getElementById('customWidth').value = '';
            document.getElementById('customHeight').value = '';
            document.getElementById('widthUnit').value = 'px';
            document.getElementById('heightUnit').value = 'px';
        }
    });

    document.getElementById('customFontSize').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            setCustomFontSize();
        }
    });
    function setTextColor(color) {
    const textarea = document.getElementById('content');
    const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
    
    if (selectedText) {
        const colorSpan = `<span style="color: ${color};">${selectedText}</span>`;
        const start = textarea.selectionStart;
        textarea.value = textarea.value.substring(0, start) + 
                        colorSpan + 
                        textarea.value.substring(textarea.selectionEnd);
    }
}

// Функции для работы со шрифтом
function setFontFamily(font) {
    if (font === 'custom') {
        document.getElementById('fontFamilyDialog').style.display = 'block';
        document.getElementById('customFontFamily').focus();
        return;
    }

    const textarea = document.getElementById('content');
    const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);

    if (selectedText) {
        const fontSpan = `<span style="font-family: '${font}';">${selectedText}</span>`;
        const start = textarea.selectionStart;
        textarea.value = textarea.value.substring(0, start) + 
                       fontSpan + 
                       textarea.value.substring(textarea.selectionEnd);
    }

    document.querySelector('.font-family-select').value = '';
}

function closeFontFamilyDialog() {
    document.getElementById('fontFamilyDialog').style.display = 'none';
    document.getElementById('customFontFamily').value = '';
}

function setCustomFontFamily() {
    const font = document.getElementById('customFontFamily').value.trim();
    if (font) {
        setFontFamily(font);
        closeFontFamilyDialog();
    } else {
        alert('Пожалуйста, введите название шрифта');
    }
}
</script>
</body>
</html>
