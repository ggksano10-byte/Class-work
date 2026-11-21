<?php
// delete.php?file=report.pdf
$name = basename($_GET['file'] ?? ''); // basename не даст выйти из папки через ../
$path = __DIR__ . '/uploads/' . $name;

if ($name !== '' && is_file($path)) {
    unlink($path);
    header('Location: index.php?msg=' . urlencode('Файл удалён'));
} else {
    header('Location: index.php?error=' . urlencode('Файл не найден'));
}
exit;
