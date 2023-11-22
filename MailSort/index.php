<?php
ini_set('memory_limit', '256M');

require_once('./src/MailProcessor.php');

use App\MailSort\MailProcessor;

$dictionaryProcessor = new MailProcessor();

if (php_sapi_name() === 'cli') {
    echo "Введите URL с данными для обработки: ";
    $fileUrl = trim(fgets(STDIN));
    $dictionaryProcessor->processFile($fileUrl);
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Сортировка почтовых файлов</title>
    </head>
    <body>
    <form method="post" enctype="multipart/form-data">
        <input name="userFile" type="file"><br>
        <button type="submit">Начать обработку</button>
    </form>
    </body>
    </html>
    <?php
    $dictionaryProcessor->handleWebUpload($_FILES['userFile']['tmp_name']);
}