<?php
// ===== PHP-часть: подготовка данных =====
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Задание 6: ограничения из php.ini
$maxFile = ini_get('upload_max_filesize');
$maxPost = ini_get('post_max_size');

// Сообщения, которые передают upload.php и delete.php
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

// Задание 4: список файлов (только файлы, без . и ..)
$files = array_values(array_filter(
    array_diff(scandir($uploadDir), ['.', '..']),
    fn($f) => is_file($uploadDir . $f)
));

// Задание 4: размер в удобном виде
function formatSize(int $bytes): string {
    $units = ['Б', 'КБ', 'МБ', 'ГБ'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
}

// Суммарный размер
$totalSize = 0;
foreach ($files as $f) {
    $totalSize += filesize($uploadDir . $f);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Файловый менеджер</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Файловый менеджер</h1>
        <p class="subtitle">Практическая работа: работа с файлами в PHP</p>
    </header>

    <!-- Сообщения об успехе и ошибках -->
    <?php if ($msg): ?>
        <div class="alert success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Форма загрузки -->
    <section class="card">
        <h2>Загрузка файла</h2>
        <div class="limits">
            Ограничения сервера: один файл — до <b><?= $maxFile ?></b>,
            весь запрос — до <b><?= $maxPost ?></b>
        </div>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="upload-form">
            <input type="file" name="userfile" required>
            <button type="submit">Загрузить</button>
        </form>
        <p class="hint">Разрешены: jpg, jpeg, png, gif, pdf, txt, docx, xlsx, zip. Не более 5 МБ.</p>
    </section>

    <!-- Список файлов -->
    <section class="card">
        <h2>Загруженные файлы</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Имя файла</th>
                        <th>Размер</th>
                        <th>Дата загрузки</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($files)): ?>
                    <tr><td colspan="5" class="empty">Файлов пока нет</td></tr>
                <?php else: ?>
                    <?php foreach ($files as $i => $name): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <a href="uploads/<?= rawurlencode($name) ?>" download>
                                    <?= htmlspecialchars($name) ?>
                                </a>
                            </td>
                            <td><?= formatSize(filesize($uploadDir . $name)) ?></td>
                            <td><?= date('d.m.Y H:i', filemtime($uploadDir . $name)) ?></td>
                            <td>
                                <a class="btn-delete"
                                   href="delete.php?file=<?= urlencode($name) ?>"
                                   onclick="return confirm('Удалить этот файл?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($files)): ?>
            <p class="total">
                Всего файлов: <?= count($files) ?>, общий размер: <?= formatSize($totalSize) ?>
            </p>
        <?php endif; ?>
    </section>

    <footer>Выполнил(а): Милованцев Тихон · Группа: ИС-381</footer>
</div>
</body>
</html>
