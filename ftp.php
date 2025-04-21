<?php
// Define the JSON file for storing credentials
define('CREDENTIALS_FILE', 'ftp.json');

// Function to save credentials to JSON file
function saveCredentials($data) {
    $data['saved_at'] = date('Y-m-d H:i:s');
    file_put_contents(CREDENTIALS_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

// Function to load credentials from JSON file
function loadCredentials() {
    if (file_exists(CREDENTIALS_FILE)) {
        return json_decode(file_get_contents(CREDENTIALS_FILE), true);
    }
    return null;
}

// Function to reset credentials
function resetCredentials() {
    if (file_exists(CREDENTIALS_FILE)) {
        unlink(CREDENTIALS_FILE);
    }
}

// Handle reset request
if (isset($_GET['reset'])) {
    resetCredentials();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the FTP upload
    header('Content-Type: application/json');
    
    $ftpServer = $_POST['ftpServer'] ?? '';
    $ftpUsername = $_POST['ftpUsername'] ?? '';
    $ftpPassword = $_POST['ftpPassword'] ?? '';
    $ftpDirectory = $_POST['ftpDirectory'] ?? '';
    $remoteFilename = $_POST['remoteFilename'] ?? 'blog.html';
    $fileName = 'blog.html';

    // Validate inputs
    if (empty($ftpServer) || empty($ftpUsername) || empty($ftpDirectory)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }

    // Sanitize remote filename
    $remoteFilename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', $remoteFilename);
    if (empty($remoteFilename)) {
        $remoteFilename = 'blog.html';
    }

    // Save credentials for future use
    if (isset($_POST['remember'])) {
        saveCredentials([
            'ftpServer' => $ftpServer,
            'ftpUsername' => $ftpUsername,
            'ftpDirectory' => $ftpDirectory,
            'remoteFilename' => $remoteFilename
        ]);
    }

    $localFile = __DIR__ . '/' . $fileName;
    if (!file_exists($localFile)) {
        echo json_encode(['success' => false, 'message' => 'blog.html not found in this directory']);
        exit;
    }

    // Sanitize FTP directory
    $ftpDirectory = '/' . trim($ftpDirectory, '/') . '/';

    // Connect to FTP server
    $connId = @ftp_connect($ftpServer);
    if (!$connId) {
        echo json_encode(['success' => false, 'message' => 'Could not connect to FTP server']);
        exit;
    }

    // Login to FTP server
    $loginResult = @ftp_login($connId, $ftpUsername, $ftpPassword);
    if (!$loginResult) {
        echo json_encode(['success' => false, 'message' => 'FTP login failed']);
        ftp_close($connId);
        exit;
    }

    // Enable passive mode
    ftp_pasv($connId, true);

    // Create directory structure if needed
    $dirParts = explode('/', trim($ftpDirectory, '/'));
    $currentDir = '';
    foreach ($dirParts as $part) {
        if (!empty($part)) {
            $currentDir .= '/' . $part;
            if (!@ftp_chdir($connId, $currentDir)) {
                if (!@ftp_mkdir($connId, $currentDir)) {
                    echo json_encode(['success' => false, 'message' => "Could not create directory: $currentDir"]);
                    ftp_close($connId);
                    exit;
                }
            }
        }
    }

    // Upload the file with custom filename
    $remoteFile = $ftpDirectory . $remoteFilename;
    if (@ftp_put($connId, $remoteFile, $localFile, FTP_BINARY)) {
        echo json_encode(['success' => true, 'message' => "blog.html uploaded as $remoteFilename to $ftpDirectory"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'There was a problem while uploading the file']);
    }

    // Close the FTP connection
    ftp_close($connId);
    exit;
}

// Load saved credentials
$savedCredentials = loadCredentials();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>blog.html FTP Uploader</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        button.reset {
            background-color: #f44336;
        }
        button.reset:hover {
            background-color: #d32f2f;
        }
        #status {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .remember {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        .remember input {
            width: auto;
            margin-right: 10px;
        }
        .saved-info {
            background-color: #e7f3fe;
            padding: 10px;
            border-left: 5px solid #2196F3;
            margin-bottom: 20px;
        }
        .filename-note {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>FTP Uploader</h1>
    
    <?php if ($savedCredentials): ?>
    <div class="saved-info">
        <p>Using saved FTP settings (last saved: <?= htmlspecialchars($savedCredentials['saved_at'] ?? 'unknown') ?>)</p>
        <p>Server: <?= htmlspecialchars($savedCredentials['ftpServer'] ?? '') ?></p>
        <p>Username: <?= htmlspecialchars($savedCredentials['ftpUsername'] ?? '') ?></p>
        <p>Directory: <?= htmlspecialchars($savedCredentials['ftpDirectory'] ?? '') ?></p>
        <p>Filename: <?= htmlspecialchars($savedCredentials['remoteFilename'] ?? 'blog.html') ?></p>
    </div>
    <?php endif; ?>
    
    <form id="uploadForm">
        <div class="form-group">
            <label for="ftpServer">FTP Сервер:</label>
            <input type="text" id="ftpServer" name="ftpServer" 
                   value="<?= htmlspecialchars($savedCredentials['ftpServer'] ?? '') ?>" 
                   placeholder="ftp.example.com" required>
        </div>
        
        <div class="form-group">
            <label for="ftpUsername">Имя пользователя:</label>
            <input type="text" id="ftpUsername" name="ftpUsername" 
                   value="<?= htmlspecialchars($savedCredentials['ftpUsername'] ?? '') ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="ftpPassword">Пароль:</label>
            <input type="password" id="ftpPassword" name="ftpPassword" required>
        </div>
        
        <div class="form-group">
            <label for="ftpDirectory">В какую директорию загрузить? (например, /blog):</label>
            <input type="text" id="ftpDirectory" name="ftpDirectory" 
                   value="<?= htmlspecialchars($savedCredentials['ftpDirectory'] ?? '') ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="remoteFilename">С каким именем загрузить?:</label>
            <input type="text" id="remoteFilename" name="remoteFilename" 
                   value="<?= htmlspecialchars($savedCredentials['remoteFilename'] ?? 'blog.html') ?>"
                   placeholder="blog.html">
            <p class="filename-note">Оставьте поле пустым или используйте "blog.html", чтобы сохранить оригинальное название. Допускаются только буквы, цифры, дефисы, подчеркивания и точки.</p>
        </div>
        
        <div class="remember">
            <input type="checkbox" id="remember" name="remember" checked>
            <label for="remember">Запомнить настройки FTP? (пароль запоминается браузером)</label>
        </div>
        
        <button type="button" id="uploadBtn">Загрузить</button>
        <button type="button" id="resetBtn" class="reset">Сбросить настройки FTP</button>
    </form>
    
    <div id="status"></div>
    
    <script>
        // Handle upload button click
        document.getElementById('uploadBtn').addEventListener('click', function() {
            const ftpServer = document.getElementById('ftpServer').value;
            const ftpUsername = document.getElementById('ftpUsername').value;
            const ftpPassword = document.getElementById('ftpPassword').value;
            const ftpDirectory = document.getElementById('ftpDirectory').value;
            const remoteFilename = document.getElementById('remoteFilename').value || 'blog.html';
            const remember = document.getElementById('remember').checked;
            
            if (!ftpServer || !ftpUsername || !ftpDirectory) {
                document.getElementById('status').textContent = 'Please fill in all required fields';
                document.getElementById('status').className = 'error';
                return;
            }
            
            // Basic filename validation
            if (!/^[a-zA-Z0-9\-_\.]+$/.test(remoteFilename)) {
                document.getElementById('status').textContent = 'Invalid filename. Only letters, numbers, hyphens, underscores and dots are allowed.';
                document.getElementById('status').className = 'error';
                return;
            }
            
            const statusDiv = document.getElementById('status');
            statusDiv.textContent = 'Uploading blog.html...';
            statusDiv.className = '';
            
            const formData = new FormData();
            formData.append('ftpServer', ftpServer);
            formData.append('ftpUsername', ftpUsername);
            formData.append('ftpPassword', ftpPassword);
            formData.append('ftpDirectory', ftpDirectory);
            formData.append('remoteFilename', remoteFilename);
            if (remember) formData.append('remember', '1');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    statusDiv.textContent = data.message;
                    statusDiv.className = 'success';
                    // Reload to show updated saved credentials
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    statusDiv.textContent = 'Error: ' + data.message;
                    statusDiv.className = 'error';
                }
            })
            .catch(error => {
                statusDiv.textContent = 'Error: ' + error.message;
                statusDiv.className = 'error';
            });
        });
        
        // Handle reset button click
        document.getElementById('resetBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to reset saved FTP settings?')) {
                window.location.href = window.location.href + '?reset=1';
            }
        });
    </script>
</body>
</html>